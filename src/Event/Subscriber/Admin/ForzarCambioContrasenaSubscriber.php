<?php

namespace App\Event\Subscriber\Admin;

use App\Entity\TrabajadorCredencial;
use App\Event\Subscriber\_Subscriber_;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

final class ForzarCambioContrasenaSubscriber extends _Subscriber_
{
    private Security $security;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(Security $security, UrlGeneratorInterface $urlGenerator)
    {
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
    }

    #[ArrayShape([KernelEvents::REQUEST => "array[]"])]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['forcePasswordChange', 0]
            ],
        ];
    }

    public function forcePasswordChange(RequestEvent $event): void
    {
        // only deal with the main request, disregard subrequests
        if (!$event->isMainRequest()) {
            return;
        }

        // if we are visiting the password change route, no need to redirect
        // otherwise we'd create an infinite redirection loop
        if ($event->getRequest()->get('_route') === 'app_change_password') {
            return;
        }

        $user = $this->security->getUser();
        // if you do not have a valid user, it means it's not an authenticated request, so it's not our concern
        if (!$user instanceof TrabajadorCredencial) {
            return;
        }

        // if it's not their first login, and they do not need to change their password, move on
        if (!$user->getForzarCambioContrasena()) {
            return;
        }

        // if we get here, it means we need to redirect them to the password change view.
        $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_change_password')));

    }
}