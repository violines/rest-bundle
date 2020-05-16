<?php

declare(strict_types=1);

namespace TerryApiBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use TerryApiBundle\Builder\ResponseBuilder;
use TerryApiBundle\Facade\SerializerFacade;
use TerryApiBundle\ValueObject\HTTPClient;
use TerryApiBundle\ValueObject\HTTPServer;

class ArrayResponseListener
{
    private HTTPServer $httpServer;

    private ResponseBuilder $responseBuilder;

    private SerializerFacade $serializerFacade;

    public function __construct(
        HTTPServer $httpServer,
        ResponseBuilder $responseBuilder,
        SerializerFacade $serializerFacade
    ) {
        $this->httpServer = $httpServer;
        $this->responseBuilder = $responseBuilder;
        $this->serializerFacade = $serializerFacade;
    }

    public function transform(ViewEvent $viewEvent): void
    {
        $controllerResult = $viewEvent->getControllerResult();

        if (!is_array($controllerResult) || !$this->arrayHasStringKey($controllerResult)) {
            return;
        }

        $viewEvent->setResponse(
            $this->createResponse(
                $controllerResult,
                $viewEvent->getRequest()
            )
        );
    }

    private function arrayHasStringKey(array $array): bool
    {
        foreach ($array as $key => $element) {
            if (is_string($key)) {
                return true;
            }

            if (is_array($element)) {
                return $this->arrayHasStringKey($element);
            }
        }

        return false;
    }

    private function createResponse(array $data, Request $request): Response
    {
        $client = HTTPClient::fromRequest($request, $this->httpServer);

        return $this->responseBuilder
            ->setContent($this->serializerFacade->serialize($data, $client))
            ->setHeaders($client->responseHeaders())
            ->getResponse();
    }
}
