<?php

namespace App\Controller;

use App\Entity\ResetPasswordRequest;
use App\Entity\User;
use App\Form\ResetPasswordRequestFormType;
use App\Services\MailerHelper;
use App\Services\MailSender;
use Doctrine\ORM\EntityManagerInterface;
use Flasher\Prime\FlasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


class ResetPasswordController extends AbstractController
{
    public function __construct (
        private EntityManagerInterface $entityManager,
        private MailSender $mailSender
    )
    {
    }

    #[Route('/mot-de-passe-oublie', name: 'app_forgot_password')]
    public function forgotPassword(
        Request $request,
        TokenGeneratorInterface $tokenGenerator,
        FlasherInterface $flasher
    ){


        if ($this->getUser ()){
            return $this->redirectToRoute ('app_home');
        }

        if ($request->get ('email')){
            $user = $this->entityManager->getRepository (User::class)->findOneByEmail($request->get ('email'));

            if($user){

                //add the requested resetpassword to database
                $resetPassword = new ResetPasswordRequest();
                $resetPassword->setUser ($user);
                $token = $tokenGenerator->generateToken ();
                $resetPassword->setToken ($token);
                $resetPassword->setCreatedAt (new \DateTime('now'));

                $this->entityManager->persist ($resetPassword);
                $this->entityManager->flush ();

                //send an email with a link to update his password

                $url = $this->generateUrl("app_reset_password", [
                    "token"=>$token
                ], UrlGeneratorInterface::ABSOLUTE_URL);



                $content = "Bonjour ".$user->getFirstname()."<br> Vous avez demander à réinitialiser votre mot de passe sur le site k-pital RH <br> <br>";
                $content .="Merci de bien vouloir cliquez sur le lien suivant pour <a href='".$url."'>mettre à jour votre mot de passe</a>.";

                $this->mailSender->send (
                    $user->getEmail(),$user->getFirstname().' '.$user->getLastname(),
                    $content,
                    "Réinitialisation de mot de passe"
                );

                $flasher->addInfo ("Un mail de réinitialisation vous a été envoyé");
                return $this->redirectToRoute ('app_login');
            }else
            {
                $flasher->addWarning ("Cette email n'existe pas!");

                return $this->redirectToRoute ('app_reset_password');
            }
        }

        return $this->render ('reset_password/forgot_password.html.twig');
    }

    #[Route('/mot-de-passe-oublie/modifier-mot-de-passe/{token}', name:'app_reset_password')]
    public function resetPassword(
        ResetPasswordRequest $resetPassword,
        Request $req,
        UserPasswordHasherInterface $userPasswordHasher,
        FlasherInterface $flasher
    ){

        if(!$resetPassword){

            return $this->redirectToRoute ('app_forgot_password');
        }

        $now =  new \DateTime('now');

        if($now > $resetPassword->getCreatedAt ()->modify("+ 3 hour")){
            $flasher->addWarning('votre demande de réinitialisation de mot de passe a expirée veuillez la renouvellez.');
            return $this->redirectToRoute('app_forgot_password');
        }

        $form = $this->createForm (ResetPasswordRequestFormType::class);
        $form->handleRequest($req);
        if($form->isSubmitted()){
            if( $form->isValid()){
                $newPassword = $form->get('new_password')->getData();

                $userPasswordHasher->hashPassword($resetPassword->getUser(),$newPassword);
                $resetPassword->getUser()->setPassword($userPasswordHasher->hashPassword($resetPassword->getUser(),$newPassword));
                $resetPassword->getUser()->setUpdateAt(new \DateTimeImmutable('now'));

                $this->entityManager->flush();
                $flasher->addSuccess('Votre mot de passe a bien été modifié. </br> vous pouvez maintenant vous connectez.');

                return $this->redirectToRoute('app_login');
            }  else{
                $flasher->addWarning('Vérifiez bien les champs');

                return $this->redirectToRoute('app_reset_password');
            }
        }

        return $this->render('reset_password/reset_password.html.twig', ['form'=>$form->createView()]);

    }
}
