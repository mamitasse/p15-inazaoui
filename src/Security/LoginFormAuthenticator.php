<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'admin_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private UserRepository $userRepository
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        // Récupération de l'email depuis le formulaire
        $email = $request->request->get('_username', '');

        // Sauvegarde pour pré-remplir en cas d'erreur
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email, function ($userIdentifier) {

                // Recherche utilisateur en base
                $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);

                if (!$user) {
                    throw new CustomUserMessageAuthenticationException('Utilisateur introuvable.');
                }

                // 🔥 BLOQUAGE SI COMPTE DÉSACTIVÉ
                if (!$user->isActive()) {
                    throw new CustomUserMessageAuthenticationException('Votre compte est bloqué.');
                }

                return $user;
            }),

            // Vérification du mot de passe
            new PasswordCredentials($request->request->get('_password', '')),

            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Redirection si l'utilisateur voulait accéder à une page protégée
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Redirection après login
        return new RedirectResponse($this->urlGenerator->generate('admin_guest_index'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}