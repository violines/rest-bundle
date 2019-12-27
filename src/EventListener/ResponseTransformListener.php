<?php

namespace TerryApiBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Serializer\SerializerInterface;

class ResponseTransformListener
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function transform(ViewEvent $viewEvent): void
    {
        $viewEvent->setResponse(
            new Response(
                $this->serialize(
                    $viewEvent->getControllerResult()
                )
            )
        );
    }

    /**
     * @param mixed $controllerResult
     */
    private function serialize($controllerResult): string
    {
        return $this->serializer->serialize($controllerResult, 'json');
    }
}
