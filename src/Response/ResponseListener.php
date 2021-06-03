<?php

declare(strict_types=1);

namespace Violines\RestBundle\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Violines\RestBundle\HttpApi\HttpApiReader;
use Violines\RestBundle\HttpApi\MissingHttpApiException;
use Violines\RestBundle\Type\ObjectList;
use Violines\RestBundle\Type\TypeException;

/**
 * @internal
 */
final class ResponseListener
{
    private HttpApiReader $httpApiReader;
    private SuccessResponseResolver $successResponseResolver;

    public function __construct(HttpApiReader $httpApiReader, SuccessResponseResolver $successResponseResolver)
    {
        $this->httpApiReader = $httpApiReader;
        $this->successResponseResolver = $successResponseResolver;
    }

    public function transform(ViewEvent $viewEvent): void
    {
        /** @var mixed $controllerResult */
        $controllerResult = $viewEvent->getControllerResult();

        if (\is_object($controllerResult)) {
            try {
                $this->httpApiReader->read(\get_class($controllerResult));
            } catch (MissingHttpApiException $e) {
                return;
            }

            $viewEvent->setResponse($this->createResponse($controllerResult, $viewEvent->getRequest()));
        }

        if (\is_array($controllerResult)) {
            try {
                $collection = ObjectList::fromArray($controllerResult);
                if (false !== $firstElement = $collection->first()) {
                    $this->httpApiReader->read(\get_class($firstElement));
                }
            } catch (TypeException | MissingHttpApiException $e) {
                return;
            }

            $viewEvent->setResponse($this->createResponse($collection->toArray(), $viewEvent->getRequest()));
        }
    }

    /**
     * @param object[]|object $data
     */
    private function createResponse($data, Request $request): Response
    {
        return $this->successResponseResolver->resolve($data, $request);
    }
}
