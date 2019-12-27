<?php

declare(strict_types=1);

namespace TerryApiBundle\ArgumentValueResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use TerryApiBundle\Annotation\StructReader;

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
        /** @var null|class-string $className */
        $className = $argument->getType();

        if (null === $className) {
            return false;
        }

        try {
            $struct = $this->structReader->read($className);
        } catch (\Throwable $e) {
            return false;
        }

        return $struct->supports;
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
