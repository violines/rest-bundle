<?php

declare(strict_types=1);

namespace TerryApiBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\SerializerInterface;
use TerryApiBundle\Struct\Error;
use TerryApiBundle\ValueObject\RequestHeaders;

class OwnedExceptionListener
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function handle(ExceptionEvent $event)
    {
        $struct = Error::fromException($event->getThrowable());

        $response = $this->createResponse(
            $event->getRequest(),
            $struct
        );

        $event->setResponse($response);
    }

    private function createResponse(Request $request, object $struct): Response
    {
        $headers = RequestHeaders::fromRequest($request);

        return new Response(
            $this->serializer->serialize($struct, $headers->serializerType()),
            Response::HTTP_BAD_REQUEST,
            $headers->responseHeaders()
        );
    }
}
