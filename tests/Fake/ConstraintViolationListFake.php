<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Fake;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationListFake implements \Iterator, ConstraintViolationListInterface
{
    private $count;

    private array $violations;

    public function add(ConstraintViolationInterface $violation)
    {
        $this->violations[] = $violation;
    }

    public function addAll(ConstraintViolationListInterface $otherList)
    {
        // test
    }

    public function get(int $offset): ConstraintViolationInterface
    {
        return $this->violations[$offset];
    }

    public function has(int $offset): bool
    {
        return isset($this->violations[$offset]);
    }

    public function set(int $offset, ConstraintViolationInterface $violation)
    {
        $this->violations[$offset] = $violation;
    }

    public function remove(int $offset)
    {
        unset($this->violations[$offset]);
    }

    public function rewind()
    {
        $this->count = 0;
    }

    public function current()
    {
        return $this->violations[$this->count];
    }

    public function key()
    {
        return $this->count;
    }

    public function next()
    {
        return $this->count++;
    }

    public function valid()
    {
        return isset($this->violations[$this->count]);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->violations[$offset]);
    }

    public function offsetGet($offset)
    {
        // test
    }

    public function offsetSet($offset, $value): void
    {
        // test
    }

    public function offsetUnset($offset): void
    {
        // test
    }

    public function count()
    {
        return \count($this->violations);
    }
}
