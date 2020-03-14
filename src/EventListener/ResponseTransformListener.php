<?php

declare(strict_types=1);

namespace TerryApiBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Serializer\SerializerInterface;
use TerryApiBundle\Annotation\StructReader;
use TerryApiBundle\Exception\AnnotationNotFoundException;
use TerryApiBundle\ValueObject\Client;
use TerryApiBundle\ValueObject\HTTPServerDefaults;

class ResponseTransformListener
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
        $client = Client::fromRequest($request, $this->httpServerDefaults);

        return new Response(
            $this->serializer->serialize($controllerResult, $client->serializerType()),
            Response::HTTP_OK,
            $client->responseHeaders()
        );
    }
}
