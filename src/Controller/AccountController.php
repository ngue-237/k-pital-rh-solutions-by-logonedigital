<?php

namespace App\Controller;

use App\Entity\CandidateResume;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Flasher\Prime\FlasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class AccountController extends AbstractController
{

    public function __construct (
        private EntityManagerInterface $entityManager,
        private FlasherInterface $flasher
    )
    {
    }

    #[Route('/mon-compte', name: 'app_account')]
    public function account(): Response
    {
        return $this->render('account/account.html.twig');
    }

    #[Route('/mon-compte/informations-personnels', name: 'app_account_setting')]
    public function accountSetting(Request $request, TokenGeneratorInterface $tokenGenerator): Response
    {
/*        $url = $request->headers->get ('referer');
        $_url = $this->generateUrl ("app_account_setting");
        dd($_url);*/
        return $this->render('account/account_settings.html.twig');
    }

    #[Route('/mon-compte/profil', name: 'app_account_resume')]
    public function accountResume(): Response
    {
        return $this->render('account/account_resume.html.twig');
    }

    #[Route('/mon-compte/modification-mot-de-passe', name: 'app_account_change_password', methods: 'POST')]
    public function accountChangePassword(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $submittedToken = $request->request->get('token');
        $currentPassword = $request->request->get ('currentPassword');
        $newPassword = $request->request->get ('newPassword');


        if ($this->isCsrfTokenValid('update-password', $submittedToken)) {
            if($userPasswordHasher->isPasswordValid ($this->getUser (),$currentPassword)){
                if(!$userPasswordHasher){
                    $this->flasher->addWarning ("Echec de modification! Veuillez choisir un mot de passe différent de l'ancien!");
                    return $this->redirect ($request->headers->get ('referer'));
                }
                $this->getUser ()->setPassword($userPasswordHasher->hashPassword ($this->getUser (),$newPassword));
                $this->getUser ()->setUpdateAt(new \DateTimeImmutable('now'));
                $this->entityManager->flush ();

                $this->flasher->addSuccess ("Mot de passe modifié avec succès!");
                return  $this->redirectToRoute ('app_login');
            }else{

                $this->flasher->addWarning ("Echec de modification! Votre mot de passe ne correspond pas!");
                return $this->redirect ($request->headers->get ('referer'));
            }
        }else{

            $this->flasher->addWarning ("Echec de modification! Vos données sont corrompues!");
            return $this->redirect ($request->headers->get ('referer'));
        }

        return $this->render('account/account_settings.html.twig');
    }

    #[Route('/mon-compte/modification-email', name: 'app_account_change_email', methods: 'POST')]
    public function accountChangeEmail(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $submittedToken = $request->request->get('token');
        $newEmail = $request->request->get ("email");
        $password = $request->request->get ("password");

        if ($this->isCsrfTokenValid('update-password', $submittedToken)) {
            if($userPasswordHasher->isPasswordValid ($this->getUser (),$password)){
                $this->getUser ()->setEmail($newEmail);
                $this->getUser ()->setUpdateAt(new \DateTimeImmutable('now'));
                $this->entityManager->flush ();

                $this->flasher->addSuccess ("Email modifié avec succès!");
                return $this->redirect ($request->headers->get ('referer'));
            }else{

                $this->flasher->addWarning ("Echec de modification! Votre mot de passe ne correspond pas!");
                return $this->redirect ($request->headers->get ('referer'));
            }
        }else{

            $this->flasher->addWarning ("Echec de modification! Vos données sont corrompues!");
            return $this->redirect ($request->headers->get ('referer'));
        }

        return $this->render('account/account_settings.html.twig');
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
            dd ($myResume);
            //$this->entityManager->flush ();

            return $this->redirectToRoute ('app_account_resume');
        }

        return $this->render('account/profil_form.html.twig',[
            'form'=>$form->createView ()
        ]);
    }


    public function addSkill(Request $request): Response
    {
        return $this->render('account/_partials/modal_skill.html.twig');
    }

}
