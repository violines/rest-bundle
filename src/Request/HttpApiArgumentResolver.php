<?php

declare(strict_types=1);

namespace TerryApiBundle\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TerryApiBundle\HttpApi\HttpApiReader;
use TerryApiBundle\HttpApi\AnnotationNotFoundException;
use TerryApiBundle\Error\ValidationException;
use TerryApiBundle\HttpApi\HttpApi;
use TerryApiBundle\Serialize\Serializer;

final class HttpApiArgumentResolver implements ArgumentValueResolverInterface
{
    private HttpApiReader $httpApiReader;
    private ObjectNormalizer $normalizer;
    private Serializer $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        HttpApiReader $httpApiReader,
        ObjectNormalizer $normalizer,
        Serializer $serializer,
        ValidatorInterface $validator
    ) {
        $this->httpApiReader = $httpApiReader;
        $this->normalizer = $normalizer;
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

        if (null === $className || !class_exists($className)) {
            throw new \LogicException('This should have been covered by self::supports(). This is a bug, please report.');
        }

        $transformed = $this->transform($request, $argument, $className);

        $this->validate($transformed);

        $result = !is_array($transformed) ? [$transformed] : $transformed;

        yield from $result;
    }

    /**
     * @param  class-string $className
     * @return mixed[]|object
     */
    private function transform(Request $request, ArgumentMetadata $argument, string $className)
    {
        $httpApi = $this->httpApiReader->read($className);

        if (HttpApi::QUERY_STRING === $httpApi->requestInfoSource) {
            return $this->normalizer->denormalize($request->query->all(), $className);
        }

        /** @var string $content */
        $content = $request->getContent();

        $contentType = ContentTypeHeader::fromString((string)$request->headers->get(ContentTypeHeader::NAME, ''));
        $type = $argument->isVariadic() ? $className . '[]' : $className;

        return $this->serializer->deserialize($content, $type, $contentType->toMimeType());
    }

    private function validate($transformed): void
    {
        $violations = $this->validator->validate($transformed);

        if (0 < count($violations)) {
            throw ValidationException::fromViolationList($violations);
        }
    }
}
