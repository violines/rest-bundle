<?php

declare(strict_types=1);

namespace TerryApiBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use TerryApiBundle\Annotation\HTTPApiReader;
use TerryApiBundle\Exception\AnnotationNotFoundException;
use TerryApiBundle\Builder\ResponseBuilder;
use TerryApiBundle\Facade\SerializerFacade;
use TerryApiBundle\HttpClient\HttpClientFactory;

class ObjectResponseListener
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

    public function transform(ViewEvent $viewEvent): void
    {
        /** @var object[]|object|array $controllerResult */
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
            /** @var object|mixed $object */
            $object = current($controllerResult);
        }

        if (!is_object($object)) {
            return false;
        }

        try {
            $this->httpApiReader->read(get_class($object));
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
        $client = $this->httpClientFactory->fromRequest($request);

        return $this->responseBuilder
            ->setContent($this->serializerFacade->serialize($data, $client))
            ->setClient($client)
            ->getResponse();
    }
}
