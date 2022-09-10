<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('default/home.html.twig');
    }

    #[Route('/a-propos-de-nous', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('default/about.html.twig');
    }

    #[Route('/nos-services', name: 'app_services')]
    public function services(): Response
    {
        return $this->render('default/services.html.twig');
    }

    #[Route('/offres-emplois', name: 'app_jobs')]
    public function jobs(): Response
    {
        return $this->render('default/jobs.html.twig');
    }

    #[Route('/offres-emplois/single', name: 'app_job_detail')]
    public function jobDetail(): Response
    {
        return $this->render('default/job-single.html.twig');
    }

    #[Route('/contactez-nous2', name: 'app_contact1')]
    public function contact(): Response
    {
        return $this->render('default/contact.html.twig');
    }

    #[Route('/mon-compte', name: 'app_account')]
    public function account(): Response
    {
        return $this->render('default/account.html.twig');
    }

    #[Route('/mon-compte/detail', name: 'app_account_single')]
    public function accountDetail(): Response
    {
        return $this->render('default/account-single.html.twig');
    }

    #[Route('/mentions-legales', name: 'app_cgu')]
    public function cgu(): Response
    {
        return $this->render('default/mention-legal.html.twig');
    }
}
