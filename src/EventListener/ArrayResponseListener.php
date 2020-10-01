<?php

declare(strict_types=1);

namespace TerryApiBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use TerryApiBundle\Builder\ResponseBuilder;
use TerryApiBundle\Facade\SerializerFacade;
use TerryApiBundle\HttpClient\HttpClientFactory;

class ArrayResponseListener
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

    public function transform(ViewEvent $viewEvent): void
    {
        /** @var object[]|object|array $controllerResult */
        $controllerResult = $viewEvent->getControllerResult();

        if (!is_array($controllerResult) || !$this->arrayHasStringKey($controllerResult)) {
            return;
        }

        $viewEvent->setResponse(
            $this->createResponse($controllerResult, $viewEvent->getRequest())
        );
    }

    /**
     * @psalm-suppress MixedAssignment
     */
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
        $client = $this->httpClientFactory->fromRequest($request);

        return $this->responseBuilder
            ->setContent($this->serializerFacade->serialize($data, $client))
            ->setClient($client)
            ->getResponse();
    }
}
