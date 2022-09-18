<?php

namespace App\Security;

use Flasher\Prime\FlasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private AuthorizationCheckerInterface $authChecker,

        private RouterInterface $router,
        private ParameterBagInterface $container,
        private FlasherInterface $flasher
    )
    {

    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
/*        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }*/

        if($this->authChecker->isGranted('ROLE_ADMIN') ){
            return new RedirectResponse($this->urlGenerator->generate('admin'));
        }
        else if ($this->authChecker->isGranted ('ROLE_USER')){


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

         //   $path = parse_url($redirectUrl, PHP_URL_PATH);
            if ($redirectUrl === $jobDetailUrl){
                $this->flasher->addSuccess ('Succés! Vous pouvez postuler à présent');
                return new RedirectResponse($redirectUrl);
            }
            $this->flasher->addSuccess('Succès !');
            return new RedirectResponse($this->urlGenerator->generate('app_account'));
        }

        $this->flasher->addSuccess ('Succès');
        return new RedirectResponse($this->urlGenerator->generate ('app_home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
