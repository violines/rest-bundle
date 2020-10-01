<?php

declare(strict_types=1);

namespace TerryApiBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use TerryApiBundle\Annotation\HTTPApiReader;
use TerryApiBundle\Builder\ResponseBuilder;
use TerryApiBundle\Exception\HTTPErrorInterface;
use TerryApiBundle\Facade\SerializerFacade;
use TerryApiBundle\HttpClient\HttpClientFactory;

class HTTPErrorListener
{
    private HttpClientFactory $httpClientFactory;

    private ResponseBuilder $responseBuilder;

    private SerializerFacade $serializerFacade;

    private HTTPApiReader $httpApiReader;

    public function __construct(
        HttpClientFactory $httpClientFactory,
        ResponseBuilder $responseBuilder,
        SerializerFacade $serializerFacade,
        HTTPApiReader $httpApiReader
    ) {
        $this->httpClientFactory = $httpClientFactory;
        $this->responseBuilder = $responseBuilder;
        $this->serializerFacade = $serializerFacade;
        $this->httpApiReader = $httpApiReader;
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
        $client = $this->httpClientFactory->fromRequest($request);

        $object = $exception->getContent();

        $this->httpApiReader->read(get_class($object));

        return $this->responseBuilder
            ->setContent($this->serializerFacade->serialize($object, $client))
            ->setStatus($exception->getHTTPStatusCode())
            ->setClient($client)
            ->getResponse();
    }
}
