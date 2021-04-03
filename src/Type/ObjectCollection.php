<?php

declare(strict_types=1);

namespace Violines\RestBundle\Type;

/**
 * @internal
 */
final class ObjectCollection
{
    /**
     * @var object[]
     */
    private $collection;

    /**
     * @param object[] $array
     */
    private function __construct(array $array)
    {
        $this->collection = $array;
    }

    /**
     * @param array<mixed> $array
     */
    public static function fromArray(array $array): self
    {
        if ([] === $array) {
            return new self([]);
        }

        $first = \current($array);

        if (!\is_object($first)) {
            throw TypeException::notObject();
        }

        $refClass = \get_class($first);

        $objectsArray = [];
        foreach ($array as $object) {
            if (!\is_object($object)) {
                throw TypeException::notObject();
            }

            if ($refClass !== \get_class($object)) {
                throw TypeException::notSameClass();
            }

            $objectsArray[] = $object;
        }

        return new self($objectsArray);
    }

    /**
     * @return object[]
     */
    public function toArray(): array
    {
        return $this->collection;
    }

    /**
     * @return object|false
     */
    public function first()
    {
        if ([] === $this->collection) {
            return false;
        }

        return $this->collection[0];
    }
}
