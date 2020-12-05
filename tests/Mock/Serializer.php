<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Mock;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Symfony\Component\Serializer\SerializerInterface;

class Serializer implements DenormalizerInterface, SerializerInterface, NormalizerInterface
{
    private $serializer;

    public function __construct()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);
        $this->serializer = new SymfonySerializer(
            [new ObjectNormalizer($classMetadataFactory, $metadataAwareNameConverter), new ArrayDenormalizer()],
            ['json' => new JsonEncoder()]
        );
    }

    public function serialize($data, string $format, array $context = [])
    {
        return $this->serializer->serialize($data, $format, $context);
    }

    public function deserialize($data, string $type, string $format, array $context = [])
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }

    public function normalize($object, ?string $format = null, array $context = [])
    {
        return $this->serializer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, ?string $format = null)
    {
        return true;
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        return $this->serializer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return true;
    }
}
