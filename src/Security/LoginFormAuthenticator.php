<?php

namespace App\Security;

use App\Entity\TrabajadorCredencial;
use App\Repository\TrabajadorCredencialRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private UrlGeneratorInterface          $urlGenerator,
        private EntityManagerInterface         $entityManager,
        private TrabajadorCredencialRepository $credencialRepository
    )
    {
    }

    public function authenticate(Request $request): PassportInterface
    {
        $usuario = $request->request->get('usuario', '');

        $this->checkCredencial($usuario);

        $request->getSession()->set(Security::LAST_USERNAME, $usuario);

        return new Passport(
            new UserBadge($usuario),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->get('_csrf_token')),
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $this->setCredencialTrabajador($token, $request);

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    /**
     * @throws Exception
     */
    private function setCredencialTrabajador(TokenInterface $token, Request $request)
    {
        /** @var TrabajadorCredencial $credencial */
        $credencial = $token->getUser();
        $credencial->setUltimoAcceso(new DateTime());
        $credencial->setUltimaConexion($request->getClientIps());
        $credencial->setNavegador($request->headers->get('user-agent'));
        $credencial->setSalt(sha1(random_int(PHP_INT_MIN, PHP_INT_MAX)));
        $this->entityManager->persist($credencial);
        $this->entityManager->flush();
    }

    private function checkCredencial(string $usuario)
    {
        /** @var TrabajadorCredencial $credencial */
        $credencial = $this->credencialRepository->findOneByUsuario($usuario);
        if (!$credencial)
            return;

        if (!$credencial->getHabilitado())
            throw new CustomUserMessageAuthenticationException('El trabajador esta deshabilitado.');

        if (!$credencial->getEstructura()->getHabilitado())
            throw new CustomUserMessageAuthenticationException('La estructura a la que pertenece está deshabilitada.');
    }
}
