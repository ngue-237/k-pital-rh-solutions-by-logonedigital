<?php

namespace App\Controller;

use App\Entity\CandidateResume;
use App\Form\ProfilType;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Flasher\Prime\FlasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class AccountController extends AbstractController
{

    public function __construct (
        private EntityManagerInterface $entityManager,
        private AuthorizationCheckerInterface $authChecker,
        private FlasherInterface $flasher
    )
    {
    }

    #[Route('/mon-compte', name: 'app_account')]
    public function account(): Response
    {
        if($this->authChecker->isGranted ('ROLE_ADMIN')){
            return $this->redirectToRoute ('admin');
        }

        $user = $this->getUser ();
        if($user->isIsBlocked() == true){
            $this->flasher->addError ('Votre compte a été bloqué');
            return $this->redirectToRoute ('app_home');
        }else if (!$user->isIsVerified()){
            $this->flasher->addWarning ('Veuillez confirmez votre email pour accéder à votre compte!');
            return $this->redirectToRoute ('app_home');
        }
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

        return $this->render('account/account_resume.html.twig',[
            'userResume'=>$this->getUser ()->getCandidateResume()
        ]);
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


}
