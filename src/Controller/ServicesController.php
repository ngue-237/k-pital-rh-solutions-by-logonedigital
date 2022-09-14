<?php

namespace App\Controller;

use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ServicesController extends AbstractController
{
    public function __construct(
    private SeoPageInterface $seoPage,
    private UrlGeneratorInterface $urlGenerator,
    )
    {
        
    }

    #[Route('/nos-services', name: 'app_services')]
    public function services(): Response
    {   
        $description = "la meilleures agence de conseils Rh au Cameroun";
        $this -> seoPage -> setTitle ("Services")
            -> addMeta ('property','og:title','les petites annonces MA.BA.CE II')
            ->addTitleSuffix("CAPITAL RH SOLUTIONS")
            ->addMeta('name', 'description', $description)
            ->addMeta('property', 'og:title', "Services-CAPITAL RH SOLUTIONS")
            ->setLinkCanonical($this->urlGenerator->generate('app_services',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:url',  $this->urlGenerator->generate('app_services',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:description',$description)
            ->setBreadcrumb('Services', []);

        return $this->render('services/services.html.twig');
    }
}
