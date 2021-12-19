<?php

declare(strict_types=1);

namespace Violines\RestBundle\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Violines\RestBundle\HttpApi\HttpApi;
use Violines\RestBundle\HttpApi\HttpApiReader;
use Violines\RestBundle\HttpApi\MissingHttpApiException;
use Violines\RestBundle\Serialize\DeserializerType;
use Violines\RestBundle\Serialize\SerializerInterface;
use Violines\RestBundle\Validation\Validator;

/**
 * @internal
 */
final class BodyArgumentResolver implements ArgumentValueResolverInterface
{
    private HttpApiReader $httpApiReader;
    private SerializerInterface $serializer;
    private Validator $validator;

    public function __construct(HttpApiReader $httpApiReader, SerializerInterface $serializer, Validator $validator)
    {
        $this->httpApiReader = $httpApiReader;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $className = $argument->getType();
        if (null === $className || !\class_exists($className) || ('' == (string)$request->getContent() && $argument->isNullable())) {
            return false;
        }

        try {
            $httpApi = $this->httpApiReader->read($className);
        } catch (MissingHttpApiException $e) {
            return false;
        }

        return HttpApi::BODY === $httpApi->getRequestInfoSource();
    }

    /**
     * @throws SupportsException  when $this->supports should have returned false
     * @throws EmptyBodyException when $request->getContent() is false|null|''
     */
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $className = $argument->getType();
        $content = (string)$request->getContent();

        if (null === $className || !\class_exists($className) || ('' == $content && $argument->isNullable())) {
            throw SupportsException::covered();
        }

        if ('' === $content) {
            throw EmptyBodyException::required();
        }

        $type = $argument->isVariadic() ? DeserializerType::array($className) : DeserializerType::object($className);
        $contentType = ContentTypeHeader::fromString((string)$request->headers->get(ContentTypeHeader::NAME, ''));

        $deserialized = $this->serializer->deserialize($content, $type, $contentType->toMimeType());

        $this->validator->validate($deserialized);

        yield from !\is_array($deserialized) ? [$deserialized] : $deserialized;
    }
}
