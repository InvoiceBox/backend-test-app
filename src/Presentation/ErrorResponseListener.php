<?php

declare(strict_types=1);

namespace BackendTestApp\Presentation;

use BackendTestApp\Domain\Exception\BackendTestAppException;
use BackendTestApp\Domain\Exception\FieldError;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ErrorResponseListener
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $httpCode = $exception->getStatusCode();
            $message = $exception->getMessage();
        } else {
            $httpCode = 500;
            $message = 'Internal Server Error';
        }
        $responseData = [
            'error' => [
                'message' => $message,
                'code' => $this->getExceptionCode($exception),
                'fields' => $this->getErrorFields($exception),
            ],
        ];

        $this->logger->error('exception', $responseData);

        $event->setResponse(new JsonResponse($responseData, $httpCode));
    }

    private function getExceptionCode(\Throwable $exception): string
    {
        $errorName = explode('\\', get_class($exception));

        return strtolower(
            preg_replace(
                '/(?<!^)[A-Z]/',
                '_$0',
                array_pop($errorName)
            )
        );
    }

    private function getErrorFields(\Throwable $exception): array
    {
        $fieldErrorCollection = null;

        if ($exception instanceof BackendTestAppException) {
            $fieldErrorCollection = $exception->getFieldErrorCollection();
        }

        if (!$fieldErrorCollection) {
            return [];
        }

        return array_map(
            fn(FieldError $fieldError) => $fieldError->toArray(),
            $fieldErrorCollection->getItems()
        );
    }
}
