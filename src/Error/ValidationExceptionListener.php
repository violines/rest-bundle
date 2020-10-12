<?php

declare(strict_types=1);

namespace TerryApiBundle\Error;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use TerryApiBundle\Negotiation\ContentNegotiator;
use TerryApiBundle\Request\AcceptHeader;
use TerryApiBundle\Response\ContentTypeHeader;
use TerryApiBundle\Response\ResponseBuilder;
use TerryApiBundle\Serialize\Serializer;

final class ValidationExceptionListener
{
    private ContentNegotiator $contentNegotiator;
    private ResponseBuilder $responseBuilder;
    private Serializer $serializer;

    public function __construct(
        ContentNegotiator $contentNegotiator,
        ResponseBuilder $responseBuilder,
        Serializer $serializer
    ) {
        $this->contentNegotiator = $contentNegotiator;
        $this->responseBuilder = $responseBuilder;
        $this->serializer = $serializer;
    }

    public function handle(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof ValidationException) {
            return;
        }

        $event->setResponse($this->createResponse($event->getRequest(), $exception));
    }

    private function createResponse(Request $request, ValidationException $exception): Response
    {
        $acceptHeader = AcceptHeader::fromString((string) $request->headers->get(AcceptHeader::NAME, ''));
        $preferredMimeType = $this->contentNegotiator->negotiate($acceptHeader);

        return $this->responseBuilder
            ->setContent($this->serializer->serialize($exception->getViolationList(), $preferredMimeType))
            ->setStatus($exception->getStatusCode())
            ->setContentType(ContentTypeHeader::fromString($preferredMimeType->toString()))
            ->getResponse();
    }
}
