<?php

declare(strict_types=1);

namespace TerryApiBundle\Annotation;

use Doctrine\Common\Annotations\Reader;
use TerryApiBundle\Exception\AnnotationNotFoundException;

class StructReader
{
    private Reader $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param class-string $className
     */
    public function read(string $className): Struct
    {
        $annotations = $this->reader->getClassAnnotations(new \ReflectionClass($className));

        foreach ($annotations as $annotation) {
            if ($annotation instanceof Struct) {
                return $annotation;
            }
        }

        throw AnnotationNotFoundException::struct($className);
    }
}
