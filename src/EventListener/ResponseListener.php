<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Security;

class ResponseListener
{
    private LoggerInterface $logger;
    private Security $security;

    public function __construct(
        LoggerInterface $logger,
        Security $security
    )
    {
        $this->logger = $logger;
        $this->security = $security;
    }


    public function onKernelResponse(ResponseEvent $responseEvent): void
    {
        $session = $responseEvent->getRequest()->getSession();
        $this->logger->info('echo 1');

        if ($session->get('remember_me_extended')) {
            $this->logger->info('echo 2');
        }
    }
}