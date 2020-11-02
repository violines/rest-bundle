<?php

declare(strict_types=1);

namespace TerryApiBundle\HttpApi;

use Doctrine\Common\Annotations\Reader;

class HttpApiReader
{
    private Reader $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param class-string $className
     */
    public function read(string $className): HttpApi
    {
        /** @var HttpApi|null $annotation */
        $annotation = $this->reader->getClassAnnotation(new \ReflectionClass($className), HttpApi::class);

        if (null !== $annotation) {
            return $annotation;
        }

        throw AnnotationNotFoundException::httpApi($className);
    }
}
