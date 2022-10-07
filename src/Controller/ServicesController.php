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

    #[Route('/nos-services', name: 'app_service_two')]
    public function services(): Response
    {   
        $description = "la meilleures agence de conseils Rh au Cameroun";
        $this -> seoPage -> setTitle ("conseils rh")
            -> addMeta ('property','og:title','conseils rh')
            ->addTitleSuffix("CAPITAL RH SOLUTIONS")
            ->addMeta('name', 'description', $description)
            ->addMeta('property', 'og:title', "Services-CAPITAL RH SOLUTIONS")
            ->setLinkCanonical($this->urlGenerator->generate('app_services',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:url',  $this->urlGenerator->generate('app_services',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:description',$description)
            ->setBreadcrumb('Services', []);

        return $this->render('services/service-two.html.twig');
    }
    #[Route('/nos-services/personnels-maison', name: 'app_service_one')]
    public function servicePersonnel(): Response
    {   
        $description = "la meilleures agence de placement de personnel de maison au Cameroun";
        $this -> seoPage -> setTitle ("mise à disposition du personnel de maison")
            -> addMeta ('property','og:title','mise à disposition du personnel de maison')
            ->addTitleSuffix("CAPITAL RH SOLUTIONS")
            ->addMeta('name', 'description', $description)
            ->addMeta('property', 'og:title', "Services-CAPITAL RH SOLUTIONS")
            ->setLinkCanonical($this->urlGenerator->generate('app_services',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:url',  $this->urlGenerator->generate('app_services',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:description',$description)
            ->setBreadcrumb('Services', []);

        return $this->render('services/service-one.html.twig');
    }
}
