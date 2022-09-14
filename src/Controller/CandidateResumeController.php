<?php

namespace App\Controller;

use App\Entity\CandidateResume;
use App\Entity\Skill;
use App\Form\ProfilType;
use App\Form\SkillType;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Flasher\Prime\FlasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CandidateResumeController extends AbstractController
{
    public function __construct (
        private EntityManagerInterface $entityManager,
        private FlasherInterface $flasher
    )
    {
    }

    #[Route('/mon-compte/carte-de-visite', name: 'app_profil')]
    public function myProfile(Request $request): Response
    {
        $user = $this->getUser ();
        $myResume = $user->getCandidateResume();

        if(!$user->getCandidateResume()){
            $myResume = new CandidateResume();
            $myResume->setUser ($user);
            $myResume->setCreatedAt (new \DateTimeImmutable(('now')));
            $myResume->setEmail ($user->getEmail());
            $myResume->setNomcomplet ($user->getLastname().' '.$user->getFirstname());

            $form = $this->createForm (ProfilType::class, $myResume);
        }else{
            $form = $this->createForm (ProfilType::class,$myResume);
        }

        $form->handleRequest ($request);
        if ($form->isSubmitted () && $form->isValid ()){
            $this->entityManager->persist ($myResume);
            //dd ($myResume);
            $this->entityManager->flush ();

            return $this->redirectToRoute ('app_account_resume');
        }

        return $this->render('account/candidate_resume/profil_form.html.twig',[
            'form'=>$form->createView ()
        ]);
    }

    #[Route('/mon-compte/mes-competences', name: 'app_skill')]
    public function addSkill(Request $request): Response
    {

        $newSkill = new Skill();
        if ($request->request->get ('token')){
            dd ($this->getUser ()->getCandidateResume()->getSkills());
            if($this->isCsrfTokenValid ('add-skill',$request->request->get ('token'))){
                $newSkill->setTitle ($request->request->get ('skillTitle'));
                $newSkill->setLevel ((int)$request->request->get ('skillLevel'));
                $newSkill->setDescription ($request->request->get ('skillDesc'));
                $newSkill->setCandidateResume ($this->getUser ()->getCandidateResume());

                $this->entityManager->persist ($newSkill);
                dd ($this->getUser ()->getCandidateResume()->getSkills());
                $this->entityManager->flush ();

                return $this->redirectToRoute ('app_account_resume');
            }
        }

        return $this->render('account/candidate_resume/modals/modal_skill.html.twig');
    }

}
