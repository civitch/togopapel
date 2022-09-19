<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;


class LoginFormAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{

    use TargetPathTrait;


    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }


    public function supports(Request $request): ?bool
    {
        return ($request->getPathInfo() === '/auth' && $request->isMethod('POST'));
    }
    

    public function authenticate(Request $request): Passport
    {
        $username   = $request->request->get('_username');
        $password   = $request->request->get('_password');

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($password),
            [
                //(new RememberMeBadge())->enable(),
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );

        
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        $user = $token->getUser();
        $user->setLastLogin(new \DateTime());
        $this->entityManager->flush();
        if($user->hasRole($this->appSecurity->getRole('super_admin')) ||
            $user->hasRole($this->appSecurity->getRole('admin')) ||
            $user->hasRole($this->appSecurity->getRole('maintenance')) ||
            $user->hasRole($this->appSecurity->getRole('moderateur'))
        ){
            return new RedirectResponse($this->urlGenerator->generate('dashboard_corporate'));
        }
        else{
            // return new RedirectResponse($this->urlGenerator->generate('main_dashboard'));
        }

        // For example:
        //return new RedirectResponse($this->urlGenerator->generate('some_route'));
        // return new RedirectResponse($this->urlGenerator->generate('main_dashboard'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        //$request->getSession()->getFlashBag()->add('note', 'You have to login in order to access this page.');
        
        $url = $this->urlGenerator->generate('admin_auth');
        return new RedirectResponse($url);
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntrypointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}