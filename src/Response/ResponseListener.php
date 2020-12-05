<?php

declare(strict_types=1);

namespace TerryApiBundle\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use TerryApiBundle\HttpApi\HttpApiReader;
use TerryApiBundle\HttpApi\MissingHttpApiException;
use TerryApiBundle\Negotiation\ContentNegotiator;
use TerryApiBundle\Request\AcceptHeader;
use TerryApiBundle\Serialize\Serializer;

final class ResponseListener
{
    private HttpApiReader $httpApiReader;
    private ContentNegotiator $contentNegotiator;
    private ResponseBuilder $responseBuilder;
    private Serializer $serializer;

    public function __construct(
        HttpApiReader $httpApiReader,
        ContentNegotiator $contentNegotiator,
        ResponseBuilder $responseBuilder,
        Serializer $serializer
    ) {
        $this->httpApiReader = $httpApiReader;
        $this->contentNegotiator = $contentNegotiator;
        $this->responseBuilder = $responseBuilder;
        $this->serializer = $serializer;
    }

    public function transform(ViewEvent $viewEvent): void
    {
        /** @var object[]|object|array $controllerResult */
        $controllerResult = $viewEvent->getControllerResult();

        if ([] !== $controllerResult && !$this->hasHttpApi($controllerResult)) {
            return;
        }

        $viewEvent->setResponse($this->createResponse($controllerResult, $viewEvent->getRequest()));
    }

    /**
     * @param object[]|object|array $controllerResult
     */
    private function hasHttpApi($controllerResult): bool
    {
        $object = $controllerResult;

        if (is_array($controllerResult)) {
            /** @var object|mixed $object */
            $object = current($controllerResult);
        }

        if (!is_object($object)) {
            return false;
        }

        try {
            $this->httpApiReader->read(get_class($object));
        } catch (MissingHttpApiException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param object[]|object|array $data
     */
    private function createResponse($data, Request $request): Response
    {
        $acceptHeader = AcceptHeader::fromString((string) $request->headers->get(AcceptHeader::NAME, ''));
        $preferredMimeType = $this->contentNegotiator->negotiate($acceptHeader);

        return $this->responseBuilder
            ->setContent($this->serializer->serialize($data, $preferredMimeType))
            ->setContentType(ContentTypeHeader::fromString($preferredMimeType->toString()))
            ->getResponse();
    }
}
