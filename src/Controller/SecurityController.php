<?php

namespace App\Controller;

use Flasher\Prime\FlasherInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct (
        private RequestStack $requestStack,
        private FlasherInterface $flasher
    )
    {
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request, SessionInterface $session): Response
    {
         if ($this->getUser()) {

             return $this->redirectToRoute('app_home');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if($error){
            $this->flasher->addError("Votre mot de passe et\ou votre adresse email est incorrecte.");
            return $this->redirectToRoute('app_login');
        }
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $url = $request->headers->get ('referer');

        if ($session->get ('redirect_url')){
            $session->remove ('redirect_url');
        }
        $session->set ('redirect_url', $url);

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(Request $request): void
    {
        $response = new Response();
        $response->setCache([
            'must_revalidate'  => true,
            'no_cache'         => true,
            'no_store'         => true,
            'public'           => false,
            'private'          => true,
            'max_age'          => 0,
        ]);

        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/connect/google', name: 'app_google_connect')]
    public function googleConnect(ClientRegistry $clientRegistry){
        //dd($clientRegistry);
        return $clientRegistry
            ->getClient('google_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect();
    }

    /**
     * After going to Google, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/connect/google/check", name="connect_google_check")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        if (!$this->getUser ()){
            return new JsonResponse(array('status'=>false, 'message'=>'User not found!'));
        }else{
            return $this->redirectToRoute ('app_home');
        }
    }
}
