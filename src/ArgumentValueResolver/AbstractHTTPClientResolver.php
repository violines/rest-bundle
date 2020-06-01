<?php

declare(strict_types=1);

namespace TerryApiBundle\ArgumentValueResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use TerryApiBundle\ValueObject\AbstractHTTPClient;
use TerryApiBundle\ValueObject\HTTPServer;

class AbstractHTTPClientResolver implements ArgumentValueResolverInterface
{
    private HTTPServer $httpServer;

    public function __construct(HTTPServer $httpServer)
    {
        $this->httpServer = $httpServer;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $className = $argument->getType();

        if (null === $className || !class_exists($className)) {
            return false;
        }

        $reflection = new \ReflectionClass($className);

        return $reflection->isSubclassOf(AbstractHTTPClient::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $className = $argument->getType();

        if (null === $className || !class_exists($className)) {
            throw new \LogicException('This should have been covered by self::supports(). This is a bug, please report.');
        }

        /** @var class-string<AbstractHTTPClient> $className */
        yield $className::fromRequest($request, $this->httpServer);
    }
}
