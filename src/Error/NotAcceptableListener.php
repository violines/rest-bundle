<?php

declare(strict_types=1);

namespace Violines\RestBundle\Error;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Violines\RestBundle\Negotiation\NotNegotiableException;
use Violines\RestBundle\Response\ResponseBuilder;
use Violines\RestBundle\Serialize\FormatException;

final class NotAcceptableListener
{
    private LoggerInterface $logger;
    private ResponseBuilder $responseBuilder;

    public function __construct(ResponseBuilder $responseBuilder, ?LoggerInterface $logger)
    {
        $this->logger = $logger ?? new NullLogger();
        $this->responseBuilder = $responseBuilder;
    }

    public function handle(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof NotNegotiableException && !$exception instanceof FormatException) {
            return;
        }

        $this->logger->log(LogLevel::DEBUG, $exception->getMessage());

        $event->setResponse($this->createResponse());
    }

    private function createResponse(): Response
    {
        return $this->responseBuilder->setStatus(Response::HTTP_NOT_ACCEPTABLE)->getResponse();
    }
}
