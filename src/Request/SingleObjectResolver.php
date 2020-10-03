<?php

declare(strict_types=1);

namespace TerryApiBundle\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TerryApiBundle\Error\ValidationException;
use TerryApiBundle\HttpApi\HttpApiReader;
use TerryApiBundle\HttpApi\AnnotationNotFoundException;
use TerryApiBundle\HttpClient\HttpClientFactory;
use TerryApiBundle\Serialize\Serializer;

final class SingleObjectResolver implements ArgumentValueResolverInterface
{
    private HttpClientFactory $httpClientFactory;
    private Serializer $serializer;
    private HttpApiReader $httpApiReader;
    private ValidatorInterface $validator;

    public function __construct(
        HttpClientFactory $httpClientFactory,
        Serializer $serializer,
        HttpApiReader $httpApiReader,
        ValidatorInterface $validator
    ) {
        $this->httpClientFactory = $httpClientFactory;
        $this->serializer = $serializer;
        $this->httpApiReader = $httpApiReader;
        $this->validator = $validator;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $className = $argument->getType();

        if (
            true === $argument->isVariadic() || null === $className
            || !class_exists($className) || !is_string($request->getContent())
        ) {
            return false;
        }

        try {
            $this->httpApiReader->read($className);
        } catch (AnnotationNotFoundException $e) {
            return false;
        }

        return true;
    }

    /**
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $className = $argument->getType();
        $content = $request->getContent();
        $client = $this->httpClientFactory->fromRequest($request);

        if (
            true === $argument->isVariadic() || null === $className
            || !class_exists($className) || !is_string($content)
        ) {
            throw new \LogicException('This should have been covered by self::supports(). This is a bug, please report.');
        }

        $object = $this->serializer->deserialize($content, $className, $client);

        $violations = $this->validator->validate($object);

        if (0 < count($violations)) {
            throw ValidationException::fromViolationList($violations);
        }

        yield $object;
    }
}
