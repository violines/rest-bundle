<?php

declare(strict_types=1);

namespace TerryApiBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Serializer\SerializerInterface;
use TerryApiBundle\Annotation\StructReader;
use TerryApiBundle\Exception\AnnotationNotFoundException;
use TerryApiBundle\Builder\ResponseBuilder;
use TerryApiBundle\ValueObject\HTTPClient;
use TerryApiBundle\ValueObject\HTTPServer;

class ResponseTransformListener
{
    private HTTPServer $httpServer;

    private ResponseBuilder $responseBuilder;

    private SerializerInterface $serializer;

    private StructReader $structReader;

    public function __construct(
        HTTPServer $httpServer,
        ResponseBuilder $responseBuilder,
        SerializerInterface $serializer,
        StructReader $structReader
    ) {
        $this->httpServer = $httpServer;
        $this->responseBuilder = $responseBuilder;
        $this->serializer = $serializer;
        $this->structReader = $structReader;
    }

    public function transform(ViewEvent $viewEvent): void
    {
        $controllerResult = $viewEvent->getControllerResult();

        if (is_object($controllerResult)) {
            $struct = $controllerResult;
        } else if (is_array($controllerResult)) {
            [$struct] = $controllerResult;
        } else {
            return;
        }

        try {
            $this->structReader->read(get_class($struct));
        } catch (AnnotationNotFoundException $e) {
            return;
        }

        $viewEvent->setResponse(
            $this->createResponse(
                $viewEvent->getRequest(),
                $controllerResult
            )
        );
    }

    private function createResponse(Request $request, $controllerResult): Response
    {
        $client = HTTPClient::fromRequest($request, $this->httpServer);

        return $this->responseBuilder
            ->setContent($this->serializer->serialize($controllerResult, $client->serializerType()))
            ->setHeaders($client->responseHeaders())
            ->getResponse();
    }
}
