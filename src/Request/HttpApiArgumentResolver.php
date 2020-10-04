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

final class HttpApiArgumentResolver implements ArgumentValueResolverInterface
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

        if (null === $className || !class_exists($className) || !is_string($request->getContent())) {
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

        if (null === $className || !class_exists($className) || !is_string($content)) {
            throw new \LogicException('This should have been covered by self::supports(). This is a bug, please report.');
        }

        $type = $argument->isVariadic() ? $className . '[]' : $className;

        /** @var object[] $deserialized */
        $deserialized = $this->serializer->deserialize($content, $type, $client);

        $violations = $this->validator->validate($deserialized);

        if (0 < count($violations)) {
            throw ValidationException::fromViolationList($violations);
        }

        $result = !is_array($deserialized) ? [$deserialized] : $deserialized;

        yield from $result;
    }
}
