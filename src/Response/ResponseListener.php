<?php

declare(strict_types=1);

namespace Violines\RestBundle\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Violines\RestBundle\HttpApi\HttpApiReader;
use Violines\RestBundle\HttpApi\MissingHttpApiException;
use Violines\RestBundle\Negotiation\ContentNegotiator;
use Violines\RestBundle\Request\AcceptHeader;
use Violines\RestBundle\Serialize\Serializer;
use Violines\RestBundle\Type\ObjectCollection;
use Violines\RestBundle\Type\TypeException;

/**
 * @internal
 */
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
        /** @var mixed $controllerResult */
        $controllerResult = $viewEvent->getControllerResult();

        if (\is_object($controllerResult)) {
            try {
                $this->httpApiReader->read(get_class($controllerResult));
            } catch (MissingHttpApiException $e) {
                return;
            }

            $viewEvent->setResponse($this->createResponse($controllerResult, $viewEvent->getRequest()));
        }

        if (\is_array($controllerResult)) {
            try {
                $collection = ObjectCollection::fromArray($controllerResult);
                if (false !== $firstElement = $collection->first()) {
                    $this->httpApiReader->read(get_class($firstElement));
                }
            } catch (TypeException | MissingHttpApiException $e) {
                return;
            }

            $viewEvent->setResponse($this->createResponse($collection->toArray(), $viewEvent->getRequest()));
        }
    }

    /**
     * @param object[]|object $data
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
