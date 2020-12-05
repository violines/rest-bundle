<?php

declare(strict_types=1);

namespace Violines\RestBundle\HttpApi;

use Doctrine\Common\Annotations\Reader;

class HttpApiReader
{
    private ?Reader $reader;

    public function __construct(?Reader $reader = null)
    {
        $this->reader = $reader;
    }

    /**
     * @param class-string $className
     *
     * @throws MissingHttpApiException when the #[HttpApi] or @HttpApi was not found in the class
     */
    public function read(string $className): HttpApi
    {
        $reflectionClass = new \ReflectionClass($className);

        if (80000 <= \PHP_VERSION_ID) {
            foreach ($reflectionClass->getAttributes(HttpApi::class) as $attribute) {
                return $attribute->newInstance();
            }
        }

        if (null === $this->reader) {
            throw AnnotationReaderNotInstalledException::doctrine();
        }

        /** @var HttpApi|null $annotation */
        $annotation = $this->reader->getClassAnnotation($reflectionClass, HttpApi::class);

        if (null !== $annotation) {
            return $annotation;
        }

        throw MissingHttpApiException::className($className);
    }
}
