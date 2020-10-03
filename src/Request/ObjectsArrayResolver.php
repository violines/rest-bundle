<?php

declare(strict_types=1);

namespace TerryApiBundle\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TerryApiBundle\HttpApi\HttpApiReader;
use TerryApiBundle\HttpApi\AnnotationNotFoundException;
use TerryApiBundle\Error\ValidationException;
use TerryApiBundle\HttpClient\HttpClientFactory;
use TerryApiBundle\Serialize\Serializer;

final class ObjectsArrayResolver implements ArgumentValueResolverInterface
{
    private HttpApiReader $httpApiReader;
    private HttpClientFactory $httpClientFactory;
    private Serializer $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        HttpApiReader $httpApiReader,
        HttpClientFactory $httpClientFactory,
        Serializer $serializer,
        ValidatorInterface $validator
    ) {
        $this->httpApiReader = $httpApiReader;
        $this->httpClientFactory = $httpClientFactory;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $className = $argument->getType();

        if (
            false === $argument->isVariadic() || null === $className
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
            false === $argument->isVariadic() || null === $className
            || !class_exists($className) || !is_string($content)
        ) {
            throw new \LogicException('This should have been covered by self::supports(). This is a bug, please report.');
        }

        /** @var object[] $objectsArray */
        $objectsArray = $this->serializer->deserialize($content, $className . '[]', $client);

        $violations = $this->validator->validate($objectsArray);

        if (0 < count($violations)) {
            throw ValidationException::fromViolationList($violations);
        }

        foreach ($objectsArray as $item) {
            yield $item;
        }
    }
}
