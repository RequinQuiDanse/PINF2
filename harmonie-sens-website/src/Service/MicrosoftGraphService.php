<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class MicrosoftGraphService
{
    private string $clientId;
    private string $clientSecret;
    private string $refreshToken;
    private ?string $accessToken = null;
    private ?\DateTimeImmutable $tokenExpiry = null;

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        string $microsoftClientId,
        string $microsoftClientSecret,
        string $microsoftRefreshToken
    ) {
        $this->clientId = $microsoftClientId;
        $this->clientSecret = $microsoftClientSecret;
        $this->refreshToken = $microsoftRefreshToken;
    }

    /**
     * Obtient un nouveau access token via le refresh token
     */
    private function getAccessToken(): string
    {
        // Si le token est encore valide, le réutiliser
        if ($this->accessToken && $this->tokenExpiry && $this->tokenExpiry > new \DateTimeImmutable()) {
            return $this->accessToken;
        }

        try {
            // Utiliser "organizations" pour les comptes Azure AD professionnels
            $response = $this->httpClient->request('POST', 'https://login.microsoftonline.com/organizations/oauth2/v2.0/token', [
                'body' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'refresh_token' => $this->refreshToken,
                    'grant_type' => 'refresh_token',
                    'scope' => 'https://graph.microsoft.com/.default offline_access'
                ]
            ]);

            $data = $response->toArray();
            $this->accessToken = $data['access_token'];
            $this->tokenExpiry = (new \DateTimeImmutable())->modify('+' . ($data['expires_in'] - 60) . ' seconds');

            return $this->accessToken;

        } catch (\Exception $e) {
            $errorDetails = $e->getMessage();
            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                try {
                    $errorDetails = $e->getResponse()->getContent(false);
                } catch (\Exception $err) {}
            }
            file_put_contents(__DIR__ . '/../../var/teams_link.txt', date('Y-m-d H:i:s') . " - TOKEN ERROR: " . $errorDetails . "\n", FILE_APPEND);
            $this->logger->error('Erreur lors du rafraîchissement du token Microsoft: ' . $errorDetails);
            throw new \RuntimeException('Impossible d\'obtenir un token d\'accès Microsoft: ' . $e->getMessage());
        }
    }

    /**
     * Récupère les événements du calendrier pour une période donnée
     */
    public function getCalendarEvents(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        // Pas de try/catch ici, on laisse l'erreur remonter pour qu'elle soit attrapée par le debug ci-dessus
        $accessToken = $this->getAccessToken();

        $response = $this->httpClient->request('GET', 'https://graph.microsoft.com/v1.0/me/calendarview', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Prefer' => 'outlook.timezone="Europe/Paris"'
            ],
            'query' => [
                'startDateTime' => $startDate->format('Y-m-d\TH:i:s'),
                'endDateTime' => $endDate->format('Y-m-d\TH:i:s'),
                '$select' => 'subject,start,end,showAs',
                '$orderby' => 'start/dateTime'
            ]
        ]);

        return $response->toArray()['value'] ?? [];
    }

    /**
     * Récupère les créneaux de disponibilité (retourne aussi les créneaux occupés)
     */
    public function getAvailableSlots(\DateTimeInterface $startDate, \DateTimeInterface $endDate, int $slotDurationMinutes = 30): array
    {
        // Récupérer les événements du calendrier
        $events = $this->getCalendarEvents($startDate, $endDate);
        
        // Heures de travail (8h - 17h)
        $workStartHour = 8;
        $workEndHour = 17;
        
        $allSlots = [];
        
        $current = \DateTime::createFromInterface($startDate);
        $end = \DateTime::createFromInterface($endDate);
        
        while ($current <= $end) {
            $dayOfWeek = (int) $current->format('N');
            
            if ($dayOfWeek >= 6) {
                $current->modify('+1 day')->setTime(0, 0);
                continue;
            }
            
            $dayStart = (clone $current)->setTime($workStartHour, 0);
            $dayEnd = (clone $current)->setTime($workEndHour, 0);
            
            $now = new \DateTime();
            if ($current->format('Y-m-d') === $now->format('Y-m-d')) {
                $currentHour = (int) $now->format('H');
                $currentMinute = (int) $now->format('i');
                $roundedHour = $currentHour + ($currentMinute > 0 ? 1 : 0);
                if ($roundedHour >= $workEndHour) {
                    $current->modify('+1 day')->setTime(0, 0);
                    continue;
                }
                if ($roundedHour > $workStartHour) {
                    $dayStart = (clone $current)->setTime($roundedHour, 0);
                }
            }
            
            $slotStart = clone $dayStart;
            while ($slotStart < $dayEnd) {
                $slotEnd = (clone $slotStart)->modify("+{$slotDurationMinutes} minutes");
                
                if ($slotEnd > $dayEnd) {
                    break;
                }
                
                $isAvailable = true;
                if (!empty($events)) {
                    foreach ($events as $event) {
                        if (!isset($event['start']['dateTime']) || !isset($event['end']['dateTime'])) {
                            continue;
                        }
                        $eventStart = new \DateTime($event['start']['dateTime']);
                        $eventEnd = new \DateTime($event['end']['dateTime']);
                        
                        if ($slotStart < $eventEnd && $slotEnd > $eventStart) {
                            if (($event['showAs'] ?? 'busy') !== 'free') {
                                $isAvailable = false;
                                break;
                            }
                        }
                    }
                }
                
                // Retourner TOUS les créneaux avec leur disponibilité
                $allSlots[] = [
                    'start' => $slotStart->format('Y-m-d\TH:i:s'),
                    'end' => $slotEnd->format('Y-m-d\TH:i:s'),
                    'display' => $slotStart->format('d/m/Y H:i') . ' - ' . $slotEnd->format('H:i'),
                    'available' => $isAvailable
                ];
                
                $slotStart = $slotEnd;
            }
            
            $current->modify('+1 day')->setTime(0, 0);
        }
        
        return $allSlots;
    }

    public function createCalendarEvent(string $subject, \DateTimeInterface $start, \DateTimeInterface $end, string $description = '', array $attendees = []): ?array {
        $accessToken = $this->getAccessToken();

        $eventData = [
            'subject' => $subject,
            'body' => [
                'contentType' => 'HTML',
                'content' => $description
            ],
            'start' => [
                'dateTime' => $start->format('Y-m-d\TH:i:s'),
                'timeZone' => 'Europe/Paris'
            ],
            'end' => [
                'dateTime' => $end->format('Y-m-d\TH:i:s'),
                'timeZone' => 'Europe/Paris'
            ],
            'isOnlineMeeting' => true,
            'onlineMeetingProvider' => 'teamsForBusiness'
        ];

        if (!empty($attendees)) {
            $eventData['attendees'] = array_map(function ($email) {
                return [
                    'emailAddress' => ['address' => $email],
                    'type' => 'required'
                ];
            }, $attendees);
        }

        $this->logger->info('Création événement calendrier avec Teams', [
            'subject' => $subject,
            'start' => $start->format('Y-m-d H:i:s'),
            'end' => $end->format('Y-m-d H:i:s'),
            'attendees' => $attendees
        ]);

        try {
            $response = $this->httpClient->request('POST', 'https://graph.microsoft.com/v1.0/me/events', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                'json' => $eventData
            ]);

            $result = $response->toArray();
            
            // Récupérer le lien Teams depuis la réponse
            $teamsLink = $result['onlineMeeting']['joinUrl'] ?? null;
            
            $this->logger->info('Événement créé avec succès', [
                'eventId' => $result['id'] ?? 'unknown',
                'teamsLink' => $teamsLink ?? 'Non disponible'
            ]);
            
            // Écrire le lien Teams
            file_put_contents(__DIR__ . '/../../var/teams_link.txt', date('Y-m-d H:i:s') . " - CALENDAR EVENT WITH TEAMS: " . ($teamsLink ?? 'Pas de lien Teams') . "\n", FILE_APPEND);
            
            return $result;
        } catch (\Exception $e) {
            $errorDetails = $e->getMessage();
            
            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                try {
                    $errorDetails = $e->getResponse()->getContent(false);
                } catch (\Exception $err) {
                    // Ignore
                }
            }
            
            file_put_contents(__DIR__ . '/../../var/teams_link.txt', date('Y-m-d H:i:s') . " - EVENT ERROR: " . $errorDetails . "\n", FILE_APPEND);
            
            $this->logger->error('Erreur création événement calendrier', [
                'error' => $errorDetails,
                'subject' => $subject
            ]);
            return null;
        }
    }

    /**
     * Crée une réunion Teams et retourne le lien de participation
     */
    private function createTeamsMeeting(string $subject, \DateTimeInterface $start, \DateTimeInterface $end, string $accessToken): ?string
    {
        // Convertir en UTC pour l'API Microsoft Graph
        $startUtc = \DateTime::createFromInterface($start);
        $startUtc->setTimezone(new \DateTimeZone('UTC'));
        $endUtc = \DateTime::createFromInterface($end);
        $endUtc->setTimezone(new \DateTimeZone('UTC'));
        
        $meetingData = [
            'subject' => $subject,
            'startDateTime' => $startUtc->format('Y-m-d\TH:i:s\Z'),
            'endDateTime' => $endUtc->format('Y-m-d\TH:i:s\Z'),
        ];
        
        file_put_contents(__DIR__ . '/../../var/teams_link.txt', date('Y-m-d H:i:s') . " - Creating Teams meeting with data: " . json_encode($meetingData) . "\n", FILE_APPEND);
        
        try {
            $response = $this->httpClient->request('POST', 'https://graph.microsoft.com/v1.0/me/onlineMeetings', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                'json' => $meetingData
            ]);
            
            $result = $response->toArray();
            
            file_put_contents(__DIR__ . '/../../var/teams_link.txt', date('Y-m-d H:i:s') . " - Teams API Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);
            
            $joinUrl = $result['joinWebUrl'] ?? null;
            
            if ($joinUrl) {
                $this->logger->info('Réunion Teams créée', ['joinUrl' => $joinUrl]);
                file_put_contents(__DIR__ . '/../../var/teams_link.txt', date('Y-m-d H:i:s') . " - TEAMS OK: " . $joinUrl . "\n", FILE_APPEND);
            }
            
            return $joinUrl;
        } catch (\Exception $e) {
            $errorDetails = $e->getMessage();
            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                try {
                    $errorDetails = $e->getResponse()->getContent(false);
                } catch (\Exception $err) {}
            }
            file_put_contents(__DIR__ . '/../../var/teams_link.txt', date('Y-m-d H:i:s') . " - TEAMS ERROR: " . $errorDetails . "\n", FILE_APPEND);
            $this->logger->error('Erreur création réunion Teams: ' . $errorDetails);
            return null;
        }
    }
}