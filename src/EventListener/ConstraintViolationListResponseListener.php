<?php

declare(strict_types=1);

namespace TerryApiBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use TerryApiBundle\Builder\ResponseBuilder;
use TerryApiBundle\Exception\ValidationException;
use TerryApiBundle\Facade\SerializerFacade;
use TerryApiBundle\HttpClient\HttpClient;
use TerryApiBundle\HttpClient\ServerSettings;

class ConstraintViolationListResponseListener
{
    private ServerSettings $httpServer;

    private ResponseBuilder $responseBuilder;

    private SerializerFacade $serializerFacade;

    public function __construct(
        ServerSettings $httpServer,
        ResponseBuilder $responseBuilder,
        SerializerFacade $serializerFacade
    ) {
        $this->httpServer = $httpServer;
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
        $client = HttpClient::fromRequest($request, $this->httpServer);

        return $this->responseBuilder
            ->setContent($this->serializerFacade->serialize($exception->violations(), $client))
            ->setStatus($exception->httpStatusCode())
            ->setClient($client)
            ->getResponse();
    }
}
