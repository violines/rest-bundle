<?php

declare(strict_types=1);

namespace TerryApiBundle\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use TerryApiBundle\HttpApi\HttpApiReader;
use TerryApiBundle\HttpApi\AnnotationNotFoundException;
use TerryApiBundle\HttpApi\HttpApi;
use TerryApiBundle\Serialize\Serializer;
use TerryApiBundle\Validation\Validator;

final class BodyArgumentResolver implements ArgumentValueResolverInterface
{
    private HttpApiReader $httpApiReader;
    private Serializer $serializer;
    private Validator $validator;

    public function __construct(
        HttpApiReader $httpApiReader,
        Serializer $serializer,
        Validator $validator
    ) {
        $this->httpApiReader = $httpApiReader;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $className = $argument->getType();
        if (null === $className || !class_exists($className)) {
            return false;
        }

        try {
            $httpApi = $this->httpApiReader->read($className);
        } catch (AnnotationNotFoundException $e) {
            return false;
        }

        return HttpApi::BODY === $httpApi->requestInfoSource;
    }

    /**
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $className = $argument->getType();
        if (null === $className || !class_exists($className)) {
            throw SupportsException::covered();
        }

        $content = (string)$request->getContent();
        $type = $argument->isVariadic() ? $className . '[]' : $className;

        if ('' === $content) {
            yield from $argument->isVariadic() ? [] : [new $className()];
            return;
        }

        $contentType = ContentTypeHeader::fromString((string)$request->headers->get(ContentTypeHeader::NAME, ''));

        /** @var object[]|object $deserialized */
        $deserialized = $this->serializer->deserialize($content, $type, $contentType->toMimeType());

        $this->validator->validate($deserialized);

        yield from !is_array($deserialized) ? [$deserialized] : $deserialized;
    }
}
