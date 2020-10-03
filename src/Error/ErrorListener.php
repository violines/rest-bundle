<?php

declare(strict_types=1);

namespace TerryApiBundle\Error;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use TerryApiBundle\HttpApi\HttpApiReader;
use TerryApiBundle\HttpClient\HttpClientFactory;
use TerryApiBundle\Response\ResponseBuilder;
use TerryApiBundle\Serialize\Serializer;

final class ErrorListener
{
    private HttpClientFactory $httpClientFactory;

    private ResponseBuilder $responseBuilder;

    private Serializer $serializer;

    private HttpApiReader $httpApiReader;

    public function __construct(
        HttpClientFactory $httpClientFactory,
        ResponseBuilder $responseBuilder,
        Serializer $serializer,
        HttpApiReader $httpApiReader
    ) {
        $this->httpClientFactory = $httpClientFactory;
        $this->responseBuilder = $responseBuilder;
        $this->serializer = $serializer;
        $this->httpApiReader = $httpApiReader;
    }

    public function handle(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof ErrorInterface) {
            return;
        }

        $response = $this->createResponse(
            $event->getRequest(),
            $exception
        );

        $event->setResponse($response);
    }

    private function createResponse(Request $request, ErrorInterface $exception): Response
    {
        $client = $this->httpClientFactory->fromRequest($request);

        $object = $exception->getContent();

        $this->httpApiReader->read(get_class($object));

        return $this->responseBuilder
            ->setContent($this->serializer->serialize($object, $client))
            ->setStatus($exception->getHTTPStatusCode())
            ->setClient($client)
            ->getResponse();
    }
}