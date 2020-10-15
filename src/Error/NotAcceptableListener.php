<?php

declare(strict_types=1);

namespace TerryApiBundle\Error;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use TerryApiBundle\Response\ResponseBuilder;
use TerryApiBundle\Serialize\FormatException;

final class NotAcceptableListener
{
    private ResponseBuilder $responseBuilder;

    public function __construct(ResponseBuilder $responseBuilder)
    {
        $this->responseBuilder = $responseBuilder;
    }

    public function handle(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof FormatException) {
            return;
        }

        $event->setResponse($this->createResponse($exception));
    }


    private function createResponse(\Exception $exception): Response
    {
        return $this->responseBuilder
            ->setContent($exception->getMessage())
            ->setStatus(Response::HTTP_NOT_ACCEPTABLE)
            ->getResponse();
    }
}
