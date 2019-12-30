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

class RequestStructResolver implements ArgumentValueResolverInterface
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

        if (null === $className || !class_exists($className) || !is_string($request->getContent())) {
            return false;
        }

        try {
            $structAnnotation = $this->structReader->read($className);
        } catch (AnnotationNotFoundException $e) {
            return false;
        }

        return $structAnnotation->supports;
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $className = $argument->getType();
        $headers = RequestHeaders::fromRequest($request);
        $content = $request->getContent();
        $isVariadic = $argument->isVariadic();

        if (null === $className || !class_exists($className) || !is_string($content)) {
            throw new \LogicException('This should have been covered by self::supports(). This is a bug, please report.');
        }

        $serializedContent = $this->serializer->deserialize(
            $content,
            $isVariadic ? $className . '[]' : $className,
            $headers->getSerializerType()
        );

        if (!$isVariadic) {
            yield $serializedContent;
            return;
        }

        foreach ($serializedContent as $instanceOfClassName) {
            yield $instanceOfClassName;
        }
    }
}
