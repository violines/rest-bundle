<?php

declare(strict_types=1);

namespace Violines\RestBundle\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Violines\RestBundle\HttpApi\HttpApi;
use Violines\RestBundle\HttpApi\HttpApiReader;
use Violines\RestBundle\HttpApi\MissingHttpApiException;
use Violines\RestBundle\Validation\Validator;

final class QueryStringArgumentResolver implements ArgumentValueResolverInterface
{
    private HttpApiReader $httpApiReader;
    private DenormalizerInterface $denormalizer;
    private Validator $validator;

    public function __construct(
        HttpApiReader $httpApiReader,
        DenormalizerInterface $denormalizer,
        Validator $validator
    ) {
        $this->httpApiReader = $httpApiReader;
        $this->denormalizer = $denormalizer;
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
        } catch (MissingHttpApiException $e) {
            return false;
        }

        return HttpApi::QUERY_STRING === $httpApi->getRequestInfoSource();
    }

    /**
     * @return \Generator
     *
     * @throws SupportsException when $this->supports should have returned false
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $className = $argument->getType();
        if (null === $className || !class_exists($className)) {
            throw SupportsException::covered();
        }

        /** @var object $denormalized */
        $denormalized = $this->denormalizer->denormalize($request->query->all(), $className);

        $this->validator->validate($denormalized);

        yield $denormalized;
    }
}
