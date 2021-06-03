<?php

declare(strict_types=1);

namespace Violines\RestBundle\Type;

/**
 * @internal
 */
final class ObjectList
{
    /**
     * @var object[]
     */
    private $list;

    /**
     * @param object[] $array
     */
    private function __construct(array $array)
    {
        $this->list = $array;
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
        return $this->list;
    }

    /**
     * @return object|false
     */
    public function first()
    {
        if ([] === $this->list) {
            return false;
        }

        return $this->list[0];
    }
}
