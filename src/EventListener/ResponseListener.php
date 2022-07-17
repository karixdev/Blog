<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\RememberMe\RememberMeHandlerInterface;

class ResponseListener
{
    private Security $security;
    private RememberMeHandlerInterface $rememberMeHandler;

    public function __construct(Security $security, RememberMeHandlerInterface $rememberMeHandler)
    {
        $this->security = $security;
        $this->rememberMeHandler = $rememberMeHandler;
    }


    public function onKernelResponse(ResponseEvent $responseEvent): void
    {
        $session = $responseEvent->getRequest()->getSession();

        if (
            $this->security->isGranted('ROLE_USER')
            && $responseEvent->getRequest()->cookies->get('REMEMBERME')
            && !$session->get('remember_me_extended')
        ) {
            $this->rememberMeHandler->createRememberMeCookie($this->security->getUser());
            $session->set('remember_me_extended', true);
        }
    }
}