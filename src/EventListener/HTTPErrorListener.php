<?php

declare(strict_types=1);

namespace TerryApiBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\SerializerInterface;
use TerryApiBundle\Annotation\StructReader;
use TerryApiBundle\Builder\ResponseBuilder;
use TerryApiBundle\Exception\HTTPErrorInterface;
use TerryApiBundle\ValueObject\HTTPClient;
use TerryApiBundle\ValueObject\HTTPServer;

class HTTPErrorListener
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

    public function handle(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof HTTPErrorInterface) {
            return;
        }

        $response = $this->createResponse(
            $event->getRequest(),
            $exception
        );

        $event->setResponse($response);
    }

    private function createResponse(Request $request, HTTPErrorInterface $exception): Response
    {
        $client = HTTPClient::fromRequest($request, $this->httpServer);

        $struct = $exception->getStruct();

        $this->structReader->read(get_class($struct));

        return $this->responseBuilder
            ->setContent($this->serializer->serialize($struct, $client->serializerType()))
            ->setStatus($exception->getHTTPStatusCode())
            ->setHeaders($client->responseHeaders())
            ->getResponse();
    }
}
