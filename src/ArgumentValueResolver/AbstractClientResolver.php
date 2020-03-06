<?php

declare(strict_types=1);

namespace TerryApiBundle\ArgumentValueResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use TerryApiBundle\ValueObject\AbstractClient;

class AbstractClientResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $className = $argument->getType();

        if (null === $className || !class_exists($className)) {
            return false;
        }

        $reflection = new \ReflectionClass($className);

        return $reflection->isSubclassOf(AbstractClient::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $className = $argument->getType();

        if (null === $className || !class_exists($className)) {
            throw new \LogicException('This should have been covered by self::supports(). This is a bug, please report.');
        }

        yield $className::fromRequest($request);
    }
}
