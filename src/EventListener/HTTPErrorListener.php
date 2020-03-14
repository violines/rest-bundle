<?php

declare(strict_types=1);

namespace TerryApiBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\SerializerInterface;
use TerryApiBundle\Annotation\StructReader;
use TerryApiBundle\Exception\HTTPErrorInterface;
use TerryApiBundle\ValueObject\Client;
use TerryApiBundle\ValueObject\HTTPServerDefaults;

class HTTPErrorListener
{
    private HTTPServerDefaults $httpServerDefaults;

    private SerializerInterface $serializer;

    private StructReader $structReader;

    public function __construct(
        HTTPServerDefaults $httpServerDefaults,
        SerializerInterface $serializer,
        StructReader $structReader
    ) {
        $this->httpServerDefaults = $httpServerDefaults;
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
        $client = Client::fromRequest($request, $this->httpServerDefaults);

        $struct = $exception->getStruct();

        $this->structReader->read(get_class($struct));

        return new Response(
            $this->serializer->serialize($struct, $client->serializerType()),
            $exception->getHTTPStatusCode(),
            $client->responseHeaders()
        );
    }
}
