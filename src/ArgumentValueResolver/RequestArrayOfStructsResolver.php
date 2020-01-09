<?php

declare(strict_types=1);

namespace TerryApiBundle\ArgumentValueResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use TerryApiBundle\Annotation\StructReader;
use TerryApiBundle\Exception\AnnotationNotFoundException;
use TerryApiBundle\ValueObject\RequestHeaders;

class RequestArrayOfStructsResolver implements ArgumentValueResolverInterface
{
    private SerializerInterface $serializer;

    private StructReader $structReader;

    public function __construct(
        SerializerInterface $serializer,
        StructReader $structReader
    ) {
        $this->serializer = $serializer;
        $this->structReader =  $structReader;
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
        $headers = RequestHeaders::fromRequest($request);

        if (
            false === $argument->isVariadic() || null === $className
            || !class_exists($className) || !is_string($content)
        ) {
            throw new \LogicException('This should have been covered by self::supports(). This is a bug, please report.');
        }

        $serializedContent = $this->serializer->deserialize(
            $content,
            $className . '[]',
            $headers->getSerializerType()
        );

        foreach ($serializedContent as $struct) {
            yield $struct;
        }
    }
}
