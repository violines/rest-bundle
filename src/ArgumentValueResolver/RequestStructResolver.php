<?php

declare(strict_types=1);

namespace TerryApiBundle\ArgumentValueResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use TerryApiBundle\Annotation\StructReader;
use TerryApiBundle\Exception\AnnotationNotFoundException;

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

        if (null === $className || !class_exists($className)) {
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
        $contentType = $request->getContentType() ?? 'json';

        if (null === $className) {
            throw new \Exception('Class could not be determined.');
        }

        $type = $argument->isVariadic() ? $className . '[]' : $className;

        $content = $this->serializer->deserialize(
            $request->getContent(),
            $type,
            $contentType
        );

        if ($argument->isVariadic()) {
            foreach ($content as $item) {
                yield $item;
            }
        } else {
            yield $content;
        }
    }
}
