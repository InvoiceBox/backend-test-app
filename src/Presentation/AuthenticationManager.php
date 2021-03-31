<?php

declare(strict_types=1);

namespace BackendTestApp\Presentation;

use BackendTestApp\Domain\Exception\Forbidden;
use BackendTestApp\Domain\Exception\Unauthorized;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthenticationManager
{
    const AUTH_HEADER = 'X-USER-ID';

    public function __construct(private RequestStack $requestStack)
    {
    }

    public function checkRequest(): void
    {
        $this->getCurrentUserId();
    }

    public function checkCurrentUserId(?int $userId): void
    {
        if ($this->getCurrentUserId() !== $userId) {
            throw new Forbidden();
        }
    }

    public function getCurrentUserId(): int
    {
        if ($userId = $this->extractCurrentUserId()) {
            return (int)$userId;
        }

        throw new Unauthorized();
    }

    private function extractCurrentUserId(): ?string
    {
        return $this->requestStack?->getCurrentRequest()->headers->get(self::AUTH_HEADER);
    }
}
