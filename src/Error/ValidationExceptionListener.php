<?php

declare(strict_types=1);

namespace Violines\RestBundle\Error;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Violines\RestBundle\Negotiation\ContentNegotiator;
use Violines\RestBundle\Request\AcceptHeader;
use Violines\RestBundle\Response\ContentTypeHeader;
use Violines\RestBundle\Response\ResponseBuilder;
use Violines\RestBundle\Serialize\Serializer;

/**
 * @internal
 */
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
