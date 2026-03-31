<?php

namespace App\Service;

use App\Entity\Appointment;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MicrosoftGraphService
{
    private ?string $accessToken = null;
    private ?\DateTimeImmutable $tokenExpiry = null;

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $microsoftClientId,
        private string $microsoftClientSecret,
        private string $microsoftRefreshToken
    ) {
    }

    private function getAccessToken(): string
    {
        if ($this->accessToken && $this->tokenExpiry && $this->tokenExpiry > new \DateTimeImmutable()) {
            return $this->accessToken;
        }

        try {
            $response = $this->httpClient->request('POST', 'https://login.microsoftonline.com/organizations/oauth2/v2.0/token', [
                'body' => [
                    'client_id' => $this->microsoftClientId,
                    'client_secret' => $this->microsoftClientSecret,
                    'refresh_token' => $this->microsoftRefreshToken,
                    'grant_type' => 'refresh_token',
                    'scope' => 'https://graph.microsoft.com/.default offline_access',
                ],
            ]);

            $data = $response->toArray();
            $this->accessToken = $data['access_token'] ?? null;

            if (!$this->accessToken) {
                throw new \RuntimeException('Token Microsoft absent de la reponse OAuth.');
            }

            $expiresIn = (int) ($data['expires_in'] ?? 3600);
            $this->tokenExpiry = (new \DateTimeImmutable())->modify('+' . max($expiresIn - 60, 60) . ' seconds');

            return $this->accessToken;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors du rafraichissement du token Microsoft', [
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Impossible d\'obtenir un token d\'acces Microsoft: ' . $e->getMessage());
        }
    }

    public function createCalendarEvent(Appointment $appointment): ?string
    {
        $startDate = \DateTimeImmutable::createFromInterface($appointment->getDesiredDate());
        $timeParts = explode(':', (string) $appointment->getDesiredTime());

        $startDate = $startDate->setTime((int) ($timeParts[0] ?? 9), (int) ($timeParts[1] ?? 0));
        $endDate = (clone $startDate)->modify('+1 hour');

        $result = $this->createCalendarEventFromData(
            'RDV - ' . $appointment->getFullName() . ' - ' . $appointment->getService(),
            $startDate,
            $endDate,
            sprintf(
                '<p><strong>Client :</strong> %s</p><p><strong>Email :</strong> %s</p><p><strong>Telephone :</strong> %s</p><p><strong>Organisation :</strong> %s</p><p><strong>Service :</strong> %s</p><p><strong>Notes :</strong> %s</p>',
                $appointment->getFullName(),
                $appointment->getEmail(),
                $appointment->getPhone() ?? 'Non renseigne',
                $appointment->getOrganization() ?? 'Non renseignee',
                $appointment->getService(),
                $appointment->getNotes() ?? 'Aucune'
            ),
            [$appointment->getEmail()]
        );

        return $result['onlineMeeting']['joinUrl'] ?? null;
    }

    public function createCalendarEventFromData(
        string $subject,
        \DateTimeInterface $start,
        \DateTimeInterface $end,
        string $description = '',
        array $attendees = []
    ): ?array {
        try {
            $accessToken = $this->getAccessToken();

            $eventData = [
                'subject' => $subject,
                'body' => [
                    'contentType' => 'HTML',
                    'content' => $description,
                ],
                'start' => [
                    'dateTime' => $start->format('Y-m-d\\TH:i:s'),
                    'timeZone' => 'Europe/Paris',
                ],
                'end' => [
                    'dateTime' => $end->format('Y-m-d\\TH:i:s'),
                    'timeZone' => 'Europe/Paris',
                ],
                'isOnlineMeeting' => true,
                'onlineMeetingProvider' => 'teamsForBusiness',
            ];

            if (!empty($attendees)) {
                $eventData['attendees'] = array_map(static fn (string $email): array => [
                    'emailAddress' => ['address' => $email],
                    'type' => 'required',
                ], $attendees);
            }

            $response = $this->httpClient->request('POST', 'https://graph.microsoft.com/v1.0/me/events', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $eventData,
            ]);

            if ($response->getStatusCode() !== 201) {
                $this->logger->warning('Echec de creation evenement Microsoft Graph', [
                    'status' => $response->getStatusCode(),
                    'subject' => $subject,
                ]);

                return null;
            }

            return $response->toArray();
        } catch (\Exception $e) {
            $this->logger->error('Erreur creation evenement calendrier Microsoft', [
                'error' => $e->getMessage(),
                'subject' => $subject,
            ]);

            return null;
        }
    }

    public function getCalendarEvents(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $accessToken = $this->getAccessToken();

        $response = $this->httpClient->request('GET', 'https://graph.microsoft.com/v1.0/me/calendarview', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Prefer' => 'outlook.timezone="Europe/Paris"',
            ],
            'query' => [
                'startDateTime' => $startDate->format('Y-m-d\\TH:i:s'),
                'endDateTime' => $endDate->format('Y-m-d\\TH:i:s'),
                '$select' => 'subject,start,end,showAs',
                '$orderby' => 'start/dateTime',
            ],
        ]);

        return $response->toArray()['value'] ?? [];
    }

    public function getAvailableSlots(\DateTimeInterface $startDate, \DateTimeInterface $endDate, int $slotDurationMinutes = 30): array
    {
        $events = $this->getCalendarEvents($startDate, $endDate);

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
                $roundedHour = (int) $now->format('H') + ((int) $now->format('i') > 0 ? 1 : 0);
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
                foreach ($events as $event) {
                    if (!isset($event['start']['dateTime'], $event['end']['dateTime'])) {
                        continue;
                    }

                    $eventStart = new \DateTime($event['start']['dateTime']);
                    $eventEnd = new \DateTime($event['end']['dateTime']);

                    if ($slotStart < $eventEnd && $slotEnd > $eventStart && (($event['showAs'] ?? 'busy') !== 'free')) {
                        $isAvailable = false;
                        break;
                    }
                }

                $allSlots[] = [
                    'start' => $slotStart->format('Y-m-d\\TH:i:s'),
                    'end' => $slotEnd->format('Y-m-d\\TH:i:s'),
                    'display' => $slotStart->format('d/m/Y H:i') . ' - ' . $slotEnd->format('H:i'),
                    'available' => $isAvailable,
                ];

                $slotStart = $slotEnd;
            }

            $current->modify('+1 day')->setTime(0, 0);
        }

        return $allSlots;
    }

    public function deleteCalendarEvent(string $eventId): bool
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = $this->httpClient->request('DELETE', 'https://graph.microsoft.com/v1.0/me/events/' . $eventId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            return $response->getStatusCode() === 204;
        } catch (\Exception $e) {
            $this->logger->error('Erreur suppression evenement Microsoft Graph', [
                'error' => $e->getMessage(),
                'eventId' => $eventId,
            ]);

            return false;
        }
    }
}
