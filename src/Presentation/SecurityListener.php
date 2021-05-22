<?php

declare(strict_types=1);

namespace BackendTestApp\Presentation;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class SecurityListener
{
    public function __construct(private AuthenticationManager $privateAuthenticationManager)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->privateAuthenticationManager->checkRequest();
    }
}
