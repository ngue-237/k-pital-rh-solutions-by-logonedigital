<?php

namespace App\Security;

use App\Entity\CandidateResume;
use App\Entity\User; // your user entity
use Doctrine\ORM\EntityManagerInterface;
use Flasher\Prime\FlasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class MyGoogleAuthenticator extends OAuth2Authenticator
{
    private $clientRegistry;
    private $entityManager;
    private $router;
    private $encoder;
    private $authChecker;
    private UrlGeneratorInterface $urlGenerator;
    

    public function __construct(
    ClientRegistry $clientRegistry, 
    EntityManagerInterface $entityManager, 
    RouterInterface $router,
    UserPasswordHasherInterface $encoder,
    AuthorizationCheckerInterface $authChecker,
    UrlGeneratorInterface $urlGenerator,
    private FlasherInterface $flasher
    )
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->encoder = $encoder;
        $this->authChecker = $authChecker;
        $this->urlGenerator = $urlGenerator;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google_main');

        //dd($client);
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);
                

                $email = $googleUser->getEmail();


                // 1) have they logged in with Google before? Easy!
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['googleId' => $googleUser->getId()]);

                if ($existingUser) {
                    return $existingUser;
                }

                // 2) do we have a matching user by email?
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

                // 3) Maybe you just want to "register" them by creating
                // a User object
                if(!$user){
                    $user = new User(); 
                    //dd($googleUser);
                    
                    $user->setEmail($googleUser->getEmail());
                    $user->setGoogleId($googleUser->getId());
                    $user->setFirstname($googleUser->getFirstname());
                    $user->setLastname($googleUser->getLastname()); 
                    $user->setIsVerified(true);
                    $user->setIsBlocked(false);

                    $candidateResume = new CandidateResume();
                    $candidateResume->setNomcomplet ($user->getFirstname ().' '.$user->getLastname ());
                    $candidateResume->setUser ($user);
                    $candidateResume->setEmail ($user->getEmail ());
                    $candidateResume->setCreatedAt (new \DateTimeImmutable('now'));

                    $user->setCandidateResume ($candidateResume);
                    $user->setRgpd(true);
                    $user->setRoles(['ROLE_USER']);
                    $hashedPassword =$this->encoder->hashPassword($user,md5(uniqid()));

                    $user->setPassword($hashedPassword);

                    $this->entityManager->persist ($candidateResume);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                }   

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        
        if($this->authChecker->isGranted('ROLE_ADMIN') ){
            return new RedirectResponse($this->urlGenerator->generate('admin'));
          }else if($this->authChecker->isGranted('ROLE_USER')){


            $redirectUrl = $request->getSession ()->get ('redirect_url');
            $jobDetailUrl = null;

            try {
                $parts = parse_url ($redirectUrl);
                $path_parts = explode ('/', $parts['path']);
                if(count ($path_parts) == 3){
                    $slug = $path_parts[2];
                    $jobDetailUrl =  $this->router->generate ('app_job_detail',['slug'=>$slug], urlGeneratorInterface::ABSOLUTE_URL);
                }
            }catch (\Throwable $exception){
                throw $exception;
            }
            //$path = parse_url($redirectUrl, PHP_URL_PATH);

            if ($redirectUrl === $jobDetailUrl){
                $this->flasher->addSuccess ('Succés');
                return new RedirectResponse($redirectUrl);
            }
            $this->flasher->addSuccess('Succès! Vous pouvez postuler à présent');
            return new RedirectResponse($this->urlGenerator->generate('app_account'));
          }

        // $targetUrl = $this->router->generate('app_home');
        $this->flasher->addSuccess('Succès !');
        return new RedirectResponse($this->urlGenerator->generate('app_account'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
}