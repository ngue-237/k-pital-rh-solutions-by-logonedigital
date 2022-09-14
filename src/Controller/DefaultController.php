<?php

namespace App\Controller;

use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{

    public function __construct(
    private SeoPageInterface $seoPage,
    private UrlGeneratorInterface $urlGenerator,
    )
    {
        
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {   
        $description = "la meilleures agence de conseils Rh au Cameroun";
        $this -> seoPage -> setTitle ("Accueil")
            -> addMeta ('property','og:title','les petites annonces MA.BA.CE II')
            ->addTitleSuffix("CAPITAL RH SOLUTIONS")
            ->addMeta('name', 'description', $description)
            ->addMeta('property', 'og:title', "Acceuil-CAPITAL RH SOLUTIONS")
            ->setLinkCanonical($this->urlGenerator->generate('app_home',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:url',  $this->urlGenerator->generate('app_home',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:description',$description)
            ->setBreadcrumb('Acceuil', []);

        return $this->render('default/home.html.twig');
    }
   
    #[Route('/a-propos-de-nous', name: 'app_about')]
    public function about(): Response
    {
        $description = "la meilleures agence de conseils Rh au Cameroun";
        $this -> seoPage -> setTitle ("A propos")
            -> addMeta ('property','og:title','les petites annonces MA.BA.CE II')
            ->addTitleSuffix("CAPITAL RH SOLUTIONS")
            ->addMeta('name', 'description', $description)
            ->addMeta('property', 'og:title', "A propos-CAPITAL RH SOLUTIONS")
            ->setLinkCanonical($this->urlGenerator->generate('app_about',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:url',  $this->urlGenerator->generate('app_about',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:description',$description)
            ->setBreadcrumb('A propos', []);

        return $this->render('default/about.html.twig');
    }
    
    #[Route('/nos-services', name: 'app_services')]
    public function services(): Response
    {   
        $description = "la meilleures agence de conseils Rh au Cameroun";
        $this -> seoPage -> setTitle ("A propos")
            -> addMeta ('property','og:title','les petites annonces MA.BA.CE II')
            ->addTitleSuffix("CAPITAL RH SOLUTIONS")
            ->addMeta('name', 'description', $description)
            ->addMeta('property', 'og:title', "A propos-CAPITAL RH SOLUTIONS")
            ->setLinkCanonical($this->urlGenerator->generate('app_about',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:url',  $this->urlGenerator->generate('app_about',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:description',$description)
            ->setBreadcrumb('A propos', []);

        return $this->render('default/services.html.twig');
    }
    
    


    #[Route('/mentions-legales', name: 'app_cgu')]
    public function cgu(): Response
    {
        return $this->render('default/mention-legal.html.twig');
    }
    
}
