<?php

declare(strict_types=1);

namespace TerryApiBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use TerryApiBundle\Annotation\StructReader;
use TerryApiBundle\Exception\AnnotationNotFoundException;
use TerryApiBundle\Builder\ResponseBuilder;
use TerryApiBundle\Facade\SerializerFacade;
use TerryApiBundle\ValueObject\HTTPClient;
use TerryApiBundle\ValueObject\HTTPServer;

class ResponseTransformListener
{
    private HTTPServer $httpServer;

    private ResponseBuilder $responseBuilder;

    private SerializerFacade $serializerFacade;

    private StructReader $structReader;

    public function __construct(
        HTTPServer $httpServer,
        ResponseBuilder $responseBuilder,
        SerializerFacade $serializerFacade,
        StructReader $structReader
    ) {
        $this->httpServer = $httpServer;
        $this->responseBuilder = $responseBuilder;
        $this->serializerFacade = $serializerFacade;
        $this->structReader = $structReader;
    }

    public function transform(ViewEvent $viewEvent): void
    {
        $controllerResult = $viewEvent->getControllerResult();

        if (!$this->hasStruct($controllerResult)) {
            return;
        }

        $viewEvent->setResponse(
            $this->createResponse($controllerResult, $viewEvent->getRequest())
        );
    }

    /**
     * @param object[]|object|array $controllerResult
     */
    private function hasStruct($controllerResult): bool
    {
        $object = $controllerResult;

        if (is_array($controllerResult)) {
            $object = current($controllerResult);
        }

        if (!is_object($object)) {
            return false;
        }

        try {
            $this->structReader->read(get_class($object));
        } catch (AnnotationNotFoundException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param object[]|object|array $data
     */
    private function createResponse($data, Request $request): Response
    {
        $client = HTTPClient::fromRequest($request, $this->httpServer);

        return $this->responseBuilder
            ->setContent($this->serializerFacade->serialize($data, $client))
            ->setHeaders($client->responseHeaders())
            ->getResponse();
    }
}
