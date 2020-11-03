<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Serialize;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SerializerMock implements DenormalizerInterface
{
    private $serializer;

    public function __construct()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);
        $this->serializer = new Serializer(
            [new ObjectNormalizer($classMetadataFactory, $metadataAwareNameConverter)],
            ['json' => new JsonEncoder()]
        );
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
