<?php

declare(strict_types=1);

namespace TerryApiBundle\Annotation;

use Doctrine\Common\Annotations\Reader;
use TerryApiBundle\Exception\AnnotationNotFoundException;

class HTTPApiReader
{
    private Reader $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param class-string $className
     */
    public function read(string $className): HTTPApi
    {
        /** @var object[] $annotations */
        $annotations = $this->reader->getClassAnnotations(new \ReflectionClass($className));

        foreach ($annotations as $annotation) {
            if ($annotation instanceof HTTPApi) {
                return $annotation;
            }
        }

        throw AnnotationNotFoundException::httpApi($className);
    }
}
