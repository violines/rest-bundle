<?php

declare(strict_types=1);

namespace TerryApiBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use TerryApiBundle\Builder\ResponseBuilder;
use TerryApiBundle\Exception\ValidationException;
use TerryApiBundle\Facade\SerializerFacade;
use TerryApiBundle\HttpClient\HttpClientFactory;

class ConstraintViolationListResponseListener
{
    private HttpClientFactory $httpClientFactory;

    private ResponseBuilder $responseBuilder;

    private SerializerFacade $serializerFacade;

    public function __construct(
        HttpClientFactory $httpClientFactory,
        ResponseBuilder $responseBuilder,
        SerializerFacade $serializerFacade
    ) {
        $this->httpClientFactory = $httpClientFactory;
        $this->responseBuilder = $responseBuilder;
        $this->serializerFacade = $serializerFacade;
    }

    public function handle(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof ValidationException) {
            return;
        }

        $response = $this->createResponse(
            $event->getRequest(),
            $exception
        );

        $event->setResponse($response);
    }

    private function createResponse(Request $request, ValidationException $exception): Response
    {
        $client = $this->httpClientFactory->fromRequest($request);

        return $this->responseBuilder
            ->setContent($this->serializerFacade->serialize($exception->violations(), $client))
            ->setStatus($exception->httpStatusCode())
            ->setClient($client)
            ->getResponse();
    }
}
