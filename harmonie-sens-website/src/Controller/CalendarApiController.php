<?php

namespace App\Controller;

use App\Service\MicrosoftGraphService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class CalendarApiController extends AbstractController
{
    public function __construct(
        private MicrosoftGraphService $graphService
    ) {}

    /**
     * Retourne les créneaux disponibles pour une semaine donnée
     */
    #[Route('/calendar/available-slots', name: 'api_calendar_slots', methods: ['GET'])]
    public function getAvailableSlots(Request $request): JsonResponse
    {
        try {
            // Gérer le weekOffset
            $weekOffset = (int) $request->query->get('weekOffset', 0);
            $duration = (int) $request->query->get('duration', 30);
            
            // Calculer le lundi de la semaine demandée
            $today = new \DateTimeImmutable('today');
            $currentDayOfWeek = (int) $today->format('N');
            $monday = $today->modify('-' . ($currentDayOfWeek - 1) . ' days');
            $monday = $monday->modify('+' . ($weekOffset * 7) . ' days');
            $friday = $monday->modify('+4 days');
            
            // Pour la semaine en cours, commencer à partir d'aujourd'hui (ignorer les jours passés)
            if ($weekOffset === 0) {
                $startDate = $today;
                // Si on est samedi ou dimanche, passer à la semaine suivante
                if ($currentDayOfWeek >= 6) {
                    $startDate = $monday->modify('+7 days');
                    $friday = $friday->modify('+7 days');
                }
            } else {
                $startDate = $monday;
            }
            
            // Label de la semaine
            $weekLabel = $monday->format('d/m') . ' - ' . $friday->format('d/m/Y');

            $slots = $this->graphService->getAvailableSlots($startDate, $friday->modify('+1 day'), $duration);
            
            // Grouper les créneaux par jour français
            $groupedSlots = [];
            foreach ($slots as $slot) {
                $slotDate = new \DateTimeImmutable($slot['start']);
                $dayName = $this->getFrenchDayName($slotDate->format('N'));
                $dateStr = $slotDate->format('d/m');
                
                if (!isset($groupedSlots[$dayName])) {
                    $groupedSlots[$dayName] = [
                        'date' => $dateStr,
                        'times' => []
                    ];
                }
                $groupedSlots[$dayName]['times'][] = [
                    'time' => $slotDate->format('H:i'),
                    'datetime' => $slot['start'],
                    'available' => $slot['available'] ?? true
                ];
            }

            return new JsonResponse([
                'success' => true,
                'slots' => $groupedSlots,
                'weekLabel' => $weekLabel
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Impossible de récupérer les disponibilités: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retourne les événements du calendrier (pour affichage)
     */
    #[Route('/calendar/events', name: 'api_calendar_events', methods: ['GET'])]
    public function getEvents(Request $request): JsonResponse
    {
        $startDate = new \DateTimeImmutable($request->query->get('start', 'today'));
        $endDate = new \DateTimeImmutable($request->query->get('end', '+4 weeks'));

        try {
            $events = $this->graphService->getCalendarEvents($startDate, $endDate);
            
            // Formater pour FullCalendar
            $formattedEvents = array_map(function ($event) {
                return [
                    'title' => 'Occupé',
                    'start' => $event['start']['dateTime'],
                    'end' => $event['end']['dateTime'],
                    'backgroundColor' => '#dc3545',
                    'borderColor' => '#dc3545'
                ];
            }, $events);

            return new JsonResponse([
                'success' => true,
                'events' => $formattedEvents
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Impossible de récupérer le calendrier'
            ], 500);
        }
    }

    private function getFrenchDayName(string $dayNumber): string
    {
        $days = [
            '1' => 'Lundi',
            '2' => 'Mardi',
            '3' => 'Mercredi',
            '4' => 'Jeudi',
            '5' => 'Vendredi',
            '6' => 'Samedi',
            '7' => 'Dimanche'
        ];
        return $days[$dayNumber] ?? '';
    }
}
