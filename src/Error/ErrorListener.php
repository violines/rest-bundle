<?php

declare(strict_types=1);

namespace Violines\RestBundle\Error;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Violines\RestBundle\HttpApi\HttpApiReader;
use Violines\RestBundle\Response\ErrorResponseResolver;

/**
 * @internal
 */
final class ErrorListener
{
    private HttpApiReader $httpApiReader;
    private ErrorResponseResolver $errorResponseResolver;

    public function __construct(HttpApiReader $httpApiReader, ErrorResponseResolver $errorResponseResolver)
    {
        $this->httpApiReader = $httpApiReader;
        $this->errorResponseResolver = $errorResponseResolver;
    }

    public function handle(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof ErrorInterface) {
            return;
        }

        $this->httpApiReader->read(\get_class($exception->getContent()));

        $event->setResponse($this->createResponse($exception, $event->getRequest()));
    }

    private function createResponse(ErrorInterface $exception, Request $request): Response
    {
        return $this->errorResponseResolver->resolve($exception, $request);
    }
}
