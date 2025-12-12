<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuoteController extends AbstractController
{
    #[Route('/faire-mon-devis', name: 'app_quote')]
    public function index(ServiceRepository $serviceRepository): Response
    {
        $services = $serviceRepository->findActiveServices();
        
        return $this->render('client/quote/index.html.twig', [
            'services' => $services,
        ]);
    }

    #[Route('/api/calculate-quote', name: 'app_quote_calculate', methods: ['POST'])]
    public function calculate(Request $request, ServiceRepository $serviceRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        
        $serviceId = $data['service_id'] ?? null;
        $duration = (float)($data['duration'] ?? 1);
        
        if (!$serviceId) {
            return $this->json(['error' => 'Service non sélectionné'], 400);
        }
        
        $service = $serviceRepository->find($serviceId);
        
        if (!$service || !$service->isActive()) {
            return $this->json(['error' => 'Service non trouvé'], 404);
        }
        
        $priceMin = $service->getPriceMin() ? (float)$service->getPriceMin() * $duration : null;
        $priceMax = $service->getPriceMax() ? (float)$service->getPriceMax() * $duration : null;
        
        return $this->json([
            'service_name' => $service->getName(),
            'duration' => $duration,
            'pricing_unit' => $service->getPricingUnit(),
            'price_min' => $priceMin,
            'price_max' => $priceMax,
            'price_range' => $this->formatPriceRange($priceMin, $priceMax),
            'pricing_details' => $service->getPricingDetails(),
        ]);
    }
    
    private function formatPriceRange(?float $priceMin, ?float $priceMax): string
    {
        if ($priceMin && $priceMax) {
            return sprintf('De %s € à %s € TTC', number_format($priceMin, 2, ',', ' '), number_format($priceMax, 2, ',', ' '));
        } elseif ($priceMin) {
            return sprintf('À partir de %s € TTC', number_format($priceMin, 2, ',', ' '));
        } elseif ($priceMax) {
            return sprintf('Jusqu\'à %s € TTC', number_format($priceMax, 2, ',', ' '));
        }
        return 'Sur devis';
    }
}
