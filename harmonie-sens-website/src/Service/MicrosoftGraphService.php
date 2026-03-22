<?php

namespace App\Service;

use App\Entity\Appointment;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MicrosoftGraphService
{
    private string $clientId;
    private string $clientSecret;
    private string $refreshToken;

    public function __construct(
        private HttpClientInterface $httpClient,
        string $microsoftClientId,
        string $microsoftClientSecret,
        string $microsoftRefreshToken,
    ) {
        $this->clientId = $microsoftClientId;
        $this->clientSecret = $microsoftClientSecret;
        $this->refreshToken = $microsoftRefreshToken;
    }

    private function getAccessToken(): string
    {
        $response = $this->httpClient->request('POST', 'https://login.microsoftonline.com/common/oauth2/v2.0/token', [
            'body' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $this->refreshToken,
                'grant_type' => 'refresh_token',
                'scope' => 'https://graph.microsoft.com/Calendars.ReadWrite https://graph.microsoft.com/OnlineMeetings.ReadWrite',
            ],
        ]);

        $data = $response->toArray();

        return $data['access_token'];
    }

    /**
     * Crée un événement calendrier avec réunion Teams.
     * Retourne le lien Teams ou null en cas d'erreur.
     */
    public function createCalendarEvent(Appointment $appointment): ?string
    {
        try {
            $accessToken = $this->getAccessToken();

            $startDate = $appointment->getDesiredDate();
            $timeParts = explode(':', $appointment->getDesiredTime());
            $startDate = $startDate->setTime((int) $timeParts[0], (int) $timeParts[1]);
            $endDate = (clone $startDate)->modify('+1 hour');

            $event = [
                'subject' => 'RDV - ' . $appointment->getFullName() . ' - ' . $appointment->getService(),
                'body' => [
                    'contentType' => 'HTML',
                    'content' => sprintf(
                        '<p><strong>Client :</strong> %s</p>
                        <p><strong>Email :</strong> %s</p>
                        <p><strong>Téléphone :</strong> %s</p>
                        <p><strong>Organisation :</strong> %s</p>
                        <p><strong>Service :</strong> %s</p>
                        <p><strong>Notes :</strong> %s</p>',
                        $appointment->getFullName(),
                        $appointment->getEmail(),
                        $appointment->getPhone() ?? 'Non renseigné',
                        $appointment->getOrganization() ?? 'Non renseignée',
                        $appointment->getService(),
                        $appointment->getNotes() ?? 'Aucune'
                    ),
                ],
                'start' => [
                    'dateTime' => $startDate->format('Y-m-d\TH:i:s'),
                    'timeZone' => 'Europe/Paris',
                ],
                'end' => [
                    'dateTime' => $endDate->format('Y-m-d\TH:i:s'),
                    'timeZone' => 'Europe/Paris',
                ],
                'attendees' => [
                    [
                        'emailAddress' => [
                            'address' => $appointment->getEmail(),
                            'name' => $appointment->getFullName(),
                        ],
                        'type' => 'required',
                    ],
                ],
                'isOnlineMeeting' => true,
                'onlineMeetingProvider' => 'teamsForBusiness',
                'isReminderOn' => true,
                'reminderMinutesBeforeStart' => 30,
            ];

            $response = $this->httpClient->request('POST', 'https://graph.microsoft.com/v1.0/me/events', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $event,
            ]);

            if ($response->getStatusCode() === 201) {
                $data = $response->toArray();
                return $data['onlineMeeting']['joinUrl'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
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
            return false;
        }
    }
}
