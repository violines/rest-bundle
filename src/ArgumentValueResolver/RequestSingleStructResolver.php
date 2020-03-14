<?php

declare(strict_types=1);

namespace TerryApiBundle\ArgumentValueResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TerryApiBundle\Annotation\StructReader;
use TerryApiBundle\Exception\AnnotationNotFoundException;
use TerryApiBundle\Exception\ValidationException;
use TerryApiBundle\ValueObject\Client;
use TerryApiBundle\ValueObject\HTTPServerDefaults;

class RequestSingleStructResolver implements ArgumentValueResolverInterface
{
    private HTTPServerDefaults $httpServerDefaults;

    private SerializerInterface $serializer;

    private StructReader $structReader;

    private ValidatorInterface $validator;

    public function __construct(
        HTTPServerDefaults $httpServerDefaults,
        SerializerInterface $serializer,
        StructReader $structReader,
        ValidatorInterface $validator
    ) {
        $this->httpServerDefaults = $httpServerDefaults;
        $this->serializer = $serializer;
        $this->structReader =  $structReader;
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
            $this->structReader->read($className);
        } catch (AnnotationNotFoundException $e) {
            return false;
        }

        return true;
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $className = $argument->getType();
        $content = $request->getContent();
        $client = Client::fromRequest($request, $this->httpServerDefaults);

        if (
            true === $argument->isVariadic() || null === $className
            || !class_exists($className) || !is_string($request->getContent())
        ) {
            throw new \LogicException('This should have been covered by self::supports(). This is a bug, please report.');
        }

        $struct = $this->serializer->deserialize(
            $content,
            $className,
            $client->deserializerType()
        );

        $violations = $this->validator->validate($struct);

        if (0 < count($violations)) {
            throw ValidationException::create($violations);
        }

        yield $struct;
    }
}
