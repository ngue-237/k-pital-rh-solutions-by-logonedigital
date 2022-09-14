<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CandidatureController extends AbstractController
{
    #[Route('/mon-compte/mes-candidatures', name: 'app_candidature')]
    public function index(): Response
    {
        return $this->render('account/candidatures/account_candidature.html.twig');
    }
    
}
