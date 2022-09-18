<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Job;
use App\Form\CandidatureType;
use Doctrine\ORM\EntityManagerInterface;
use Flasher\Prime\FlasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CandidatureController extends AbstractController
{
    public function __construct (
        private EntityManagerInterface $entityManager,
        private FlasherInterface $flasher
    )
    {
    }

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

    #[Route('/mon-compte/mes-candidatures/candidature/{id}', name: 'app_delete_candidature')]
    public function deleteApplication(Request $request, $id)
    {
        $candidature = $this->entityManager->getRepository (Candidature::class)->find ($id);
        if(!$candidature){
            throw $this->createNotFoundException ("Erreur! Cette candidature n'existe pas!");
        }

        if ($request->request->get ('token')){

            if($this->isCsrfTokenValid ('delete-candidature',$request->request->get ('token'))){

                $this->entityManager->remove ($candidature);
                $this->entityManager->flush ();

                $this->flasher->addSuccess ("Succés!");
                return $this->redirectToRoute ('app_candidature');
            }else{
                $this->flasher->addError ("Une erreur s'est produite!! Demande corrompue");
                return $this->redirectToRoute ('app_candidature');
            }
        }


        return $this->render('account/candidatures/_deletemodal.html.twig',[
            'candidature'=>$candidature,
        ]);
    }
}
