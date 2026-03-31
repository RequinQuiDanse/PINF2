<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'app_sitemap', defaults: ['_format' => 'xml'])]
    public function index(): Response
    {
        $urls = [];
        $hostname = 'https://3s-managers.fr';
        
        // Pages principales avec priorité élevée
        $urls[] = [
            'loc' => $this->generateUrl('app_home', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'lastmod' => date('Y-m-d'),
            'changefreq' => 'weekly',
            'priority' => '1.0'
        ];
        
        // Pages services
        $services = [
            ['route' => 'app_services_direction', 'priority' => '0.9'],
            ['route' => 'app_services_diagnostic', 'priority' => '0.9'],
            ['route' => 'app_services_formations', 'priority' => '0.9'],
            ['route' => 'app_services_accompagnement', 'priority' => '0.9'],
        ];
        
        foreach ($services as $service) {
            $urls[] = [
                'loc' => $this->generateUrl($service['route'], [], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => date('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => $service['priority']
            ];
        }
        
        // Pages cabinet
        $cabinetPages = [
            ['route' => 'app_cabinet_presentation', 'priority' => '0.8'],
            ['route' => 'app_cabinet_valeurs', 'priority' => '0.7'],
            ['route' => 'app_cabinet_methodologie', 'priority' => '0.7'],
            ['route' => 'app_cabinet_secteurs', 'priority' => '0.8'],
        ];
        
        foreach ($cabinetPages as $page) {
            $urls[] = [
                'loc' => $this->generateUrl($page['route'], [], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => date('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => $page['priority']
            ];
        }
        
        // Page contact
        $urls[] = [
            'loc' => $this->generateUrl('app_contact', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'lastmod' => date('Y-m-d'),
            'changefreq' => 'monthly',
            'priority' => '0.8'
        ];
        
        $response = new Response(
            $this->renderView('sitemap/sitemap.xml.twig', ['urls' => $urls]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/xml']
        );
        
        return $response;
    }
}
