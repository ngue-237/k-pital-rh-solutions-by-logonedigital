<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Services\MailSender;
use Doctrine\ORM\EntityManagerInterface;
use Flasher\Prime\FlasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{
    public function __construct (
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepo,
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailSender $sender,
        private FlasherInterface $flasher
    )
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->add("password", RepeatedType::class, [

            'type'=>PasswordType::class,
            'invalid_message'=> 'le mot de passe et la confirmation doivent être identique.',
            'first_options'=>[
                'constraints'=>[
                    new Regex(
                        [
                            "pattern"=>"/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,20}$/",
                            "match"=>true,
                            "message"=>"mot de passe invalid."
                        ]
                    ),
                    new NotBlank([
                        'message' => 'Entrez votre mot de passe.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'help'=> 'Le mot de passe doit contenir au moins 8 caractères, dont au moins: une Majuscule, un chiffre, un caractère spécial.',
            ],
            'second_options'=>[

            ]
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $user->getPassword ()
                )
            );

            $user->setRoles(["ROLE_USER"]);
            $user->setCreatedAt(new \DateTimeImmutable('now'));
            $user->setUpdateAt(new \DateTimeImmutable('now'));
            $user->setIsVerified(false);
            $user->setIsBlocked(false);


            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
/*
            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $content = "Bonjour ".$user->getFirstname().' '.$user->getLastname()."<br> Nous vous remercions pour votre inscription sur le site MA.BA.CE.&#x2161; <br> <br>";
            $content .="Merci de bien vouloir cliquez sur le lien suivant pour <a href='".$signatureComponents->getSignedUrl()."'>afin de valider votre email</a>.";

            $this->sender->send(
                $user->getEmail(),
                $user->getFirstname().' '.$user->getLastname(),
                $content,
                "vérification d'e-mail"
            );*/
            $this->flasher->addInfo('Veuillez confirmer votre email.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path:'/verify', name:'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        VerifyEmailHelperInterface $verifyEmailHelper,
    ):Response
    {
        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }

        // Do not get the User's Id or Email Address from the Request object
        try {
            $verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('verify_email_error', $e->getReason());

            return $this->redirectToRoute('app_register');
        }

        // Mark your user as verified. e.g. switch a User::verified property to true
        $user->setIsVerifyEd(true);
        $entityManager->flush();

        $this->flasher->addSuccess('Votre email a bien été confirmé.');

        return $this->redirectToRoute('app_home');
    }
}
