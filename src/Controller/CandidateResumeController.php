<?php

namespace App\Controller;

use App\Entity\CandidateResume;
use App\Entity\Language;
use App\Entity\Skill;
use App\Form\ProfilType;
use App\Form\SkillType;
use App\Repository\JobRepository;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Flasher\Prime\FlasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CandidateResumeController extends AbstractController
{
    public function __construct (
        private EntityManagerInterface $entityManager,
        private FlasherInterface $flasher
    )
    {
    }

    #[Route('/mon-compte/carte-de-visite', name: 'app_profil')]
    public function myProfile(Request $request, SluggerInterface $slugger, FileUploader $fileUploader): Response
    {
        $user = $this->getUser ();
        $myResume = $user->getCandidateResume();

        $form = $this->createForm (ProfilType::class,$myResume);

        $form->handleRequest ($request);

        if ($form->isSubmitted () && $form->isValid ()){
            $cvFile = $form->get('cv')->getData();
            $imageFile = $form->get ('photo')->getData ();

            if ($imageFile){
                $pictureName = $fileUploader->upload ($imageFile,'candidates_images_dir');
                $myResume->setPhoto($pictureName);
            }

            if ($cvFile) {
                $cvFileName = $fileUploader->upload($cvFile,'cvs_directory');
                $myResume->setCv($cvFileName);
            }
            $user->setCandidateResume($myResume);
            $this->entityManager->flush ();

            return $this->redirectToRoute ('app_account_resume');
        }

        return $this->render('account/candidate_resume/modals/update_resume.html.twig',[
            'form'=>$form->createView ()
        ]);
    }

    #[Route('/mon-compte/mes-competences/ajouter-une-competence', name: 'app_profil_add_skill')]
    public function addSkill(Request $request): Response
    {

        $newSkill = new Skill();
        if ($request->request->get ('token')){
            if($this->isCsrfTokenValid ('add-skill',$request->request->get ('token'))){
                $newSkill->setTitle ($request->request->get ('skillTitle'));
                $newSkill->setLevel ((int)$request->request->get ('skillLevel'));
                $newSkill->setDescription ($request->request->get ('skillDesc'));
                $newSkill->setCandidateResume ($this->getUser ()->getCandidateResume());

                $this->entityManager->persist ($newSkill);
                $this->entityManager->flush ();

                return $this->redirectToRoute ('app_account_resume');
            }
        }

        return $this->render('account/candidate_resume/modals/skills/modal_addskill.html.twig');
    }

    #[Route('/mon-compte/mes-competences/modifier-une-competence/{id}', name: 'app_profil_update_skill')]
    public function updateSkill(Request $request, $id): Response
    {
        $skillToUpdate = $this->entityManager->getRepository (Skill::class)->find ($id);
        if (!$skillToUpdate){
            throw $this->createNotFoundException ("Erreur! Compétence n'existe pas!");
        }

        if ($request->request->get ('token')){

            if($this->isCsrfTokenValid ('update-skill',$request->request->get ('token'))){
                $skillToUpdate->setTitle ($request->request->get ('skillTitle'));
                $skillToUpdate->setLevel ((int)$request->request->get ('skillLevel'));
                $skillToUpdate->setDescription ($request->request->get ('skillDesc'));
                $skillToUpdate->setCandidateResume ($this->getUser ()->getCandidateResume());

                $this->entityManager->flush ();
                return $this->redirectToRoute ('app_account_resume');
            }
        }

        return $this->render('account/candidate_resume/modals/skills/modal_updateskill.html.twig',[
            'skill'=>$skillToUpdate
        ]);
    }

    #[Route('/mon-compte/mes-competences/supprimer-une-competence/{id}', name: 'app_profil_delete_skill')]
    public function deleteSkill(Request $request, $id){

        $skillToDelete = $this->entityManager->getRepository (Skill::class)->find ($id);
        if (!$skillToDelete){
            throw $this->createNotFoundException ("Erreur! Compétence n'existe pas!");
        }

        if ($request->request->get ('token')){

            if($this->isCsrfTokenValid ('delete-skill',$request->request->get ('token'))){

                $this->entityManager->remove ($skillToDelete);
                $this->entityManager->flush ();
                return $this->redirectToRoute ('app_account_resume');
            }
        }

        return $this->render('account/candidate_resume/modals/skills/modal_deleteskill.html.twig',[
            'skill'=>$skillToDelete
        ]);
    }

    #[Route('/mon-compte/mes-competences/ajouter-une-langue', name: 'app_profil_add_language')]
    public function addLanguage(Request $request): Response
    {

        $newLanguage = new Language();
        if ($request->request->get ('token')){
            if($this->isCsrfTokenValid ('add-language',$request->request->get ('token'))){
                $newLanguage->setName ($request->request->get ('languageName'));
                $newLanguage->setLanguagewrite ($request->request->get ('languagewrite'));
                $newLanguage->setLanguagespeak ($request->request->get ('languagespeak'));
                $newLanguage->setCandidateResume ($this->getUser ()->getCandidateResume());

                $this->entityManager->persist ($newLanguage);
                $this->entityManager->flush ();

                return $this->redirectToRoute ('app_account_resume');
            }
        }

        return $this->render('account/candidate_resume/modals/languages/_addlanguage.html.twig');
    }

    #[Route('/mon-compte/mes-competences/modifier-une-langue/{id}', name: 'app_profil_update_language')]
    public function updateLanguage(Request $request, $id): Response
    {
        $languageToUpdate = $this->entityManager->getRepository (Language::class)->find ($id);
        if (!$languageToUpdate){
            throw $this->createNotFoundException ("Erreur! Langue n'existe pas!");
        }

        if ($request->request->get ('token')){

            if($this->isCsrfTokenValid ('update-language',$request->request->get ('token'))){
                $languageToUpdate->setName ($request->request->get ('languageName'));
                $languageToUpdate->setLanguagewrite ($request->request->get ('languagewrite'));
                $languageToUpdate->setLanguagespeak ($request->request->get ('languagespeak'));
                $languageToUpdate->setCandidateResume ($this->getUser ()->getCandidateResume());
                dd ($languageToUpdate);
                $this->entityManager->flush ();
                return $this->redirectToRoute ('app_account_resume');
            }
        }

        return $this->render('account/candidate_resume/modals/languages/_updatelanguage.html.twig',[
            'language'=>$languageToUpdate
        ]);
    }

    #[Route('/mon-compte/mes-competences/supprimer-une-langue/{id}', name: 'app_profil_delete_language')]
    public function deleteLanguage(Request $request, $id){

        $languageToDelete = $this->entityManager->getRepository (Language::class)->find ($id);
        if (!$languageToDelete){
            throw $this->createNotFoundException ("Erreur! Langue n'existe pas!");
        }

        if ($request->request->get ('token')){

            if($this->isCsrfTokenValid ('delete-language',$request->request->get ('token'))){

                $this->entityManager->remove ($languageToDelete);
                $this->entityManager->flush ();
                return $this->redirectToRoute ('app_account_resume');
            }
        }

        return $this->render('account/candidate_resume/modals/languages/_deletelanguage.html.twig',[
            'language'=>$languageToDelete
        ]);
    }

}
