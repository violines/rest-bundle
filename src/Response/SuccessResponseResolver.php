<?php

declare(strict_types=1);

namespace Violines\RestBundle\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Violines\RestBundle\Negotiation\ContentNegotiator;
use Violines\RestBundle\Request\AcceptHeader;
use Violines\RestBundle\Serialize\SerializerInterface;

final class SuccessResponseResolver
{
    private ContentNegotiator $contentNegotiator;
    private ResponseBuilder $responseBuilder;
    private SerializerInterface $serializer;

    public function __construct(ContentNegotiator $contentNegotiator, ResponseBuilder $responseBuilder, SerializerInterface $serializer)
    {
        $this->contentNegotiator = $contentNegotiator;
        $this->responseBuilder = $responseBuilder;
        $this->serializer = $serializer;
    }

    /**
     * @param object[]|object $data
     */
    public function resolve($data, Request $request): Response
    {
        $acceptHeader = AcceptHeader::fromString((string)$request->headers->get(AcceptHeader::NAME, ''));
        $preferredMimeType = $this->contentNegotiator->negotiate($acceptHeader);

        return $this->responseBuilder
            ->setContent($this->serializer->serialize($data, $preferredMimeType))
            ->setContentType(ContentTypeHeader::fromString($preferredMimeType->toString()))
            ->getResponse();
    }
}
