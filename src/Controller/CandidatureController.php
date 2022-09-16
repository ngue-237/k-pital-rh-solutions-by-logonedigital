<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Job;
use App\Form\CandidatureType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CandidatureController extends AbstractController
{
    #[Route('/mon-compte/mes-candidatures', name: 'app_candidature')]
    public function index(): Response
    {
        return $this->render('account/candidatures/account_candidature.html.twig',[
            'candidatures'=>$this->getUser ()->getCandidateResume()->getCandidatures(),
        ]);
    }

/*    #[Route('/offres-emplois/ma-candidature/{slug}', name: 'app_candidature_apply')]
    public function apply(Request $request, Job $job)
    {
        $candidature = new Candidature();

        $form =  $this->createForm (CandidatureType::class, $candidature);
        $form->handleRequest ($request);

        if($form->isSubmitted ()){
            if($form->isValid ()){
                dd ("sub and valid");
            }
        }else{
            dd ("invalid");
        }
        return $this->render('account/candidatures/account_candidature.html.twig',[
            'job'=>$job,
            'form'=>$form->createView ()
        ]);
    }*/
}
