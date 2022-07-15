<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\RememberMe\RememberMeHandlerInterface;

class ResponseListener
{
    private Security $security;
    private RememberMeHandlerInterface $rememberMeHandler;
    private LoggerInterface $logger;

    public function __construct(Security $security, RememberMeHandlerInterface $rememberMeHandler, LoggerInterface $logger)
    {
        $this->security = $security;
        $this->rememberMeHandler = $rememberMeHandler;
        $this->logger = $logger;
    }

    public function onKernelResponse(ResponseEvent $responseEvent): void
    {
        $session = $responseEvent->getRequest()->getSession();
        $this->logger->info('echo 1');

        if (
            $this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')
            && !$session->get('remember_me_extended')
        ) {
            $this->logger->info('echo 2');

            $session->set('remember_me_extended', true);

            $this->rememberMeHandler
                ->createRememberMeCookie(
                    $this->security->getUser()
                )
            ;
        }
    }
}