<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Stubs;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationListStub implements \Iterator, ConstraintViolationListInterface
{
    public function add(ConstraintViolationInterface $violation)
    {
    }

    public function addAll($otherList)
    {
    }

    public function get(int $offset)
    {
    }

    public function has(int $offset)
    {
    }

    public function set(int $offset, ConstraintViolationInterface $violation)
    {
    }

    public function remove(int $offset)
    {
    }

    public function rewind()
    {
    }

    public function current()
    {
    }

    public function key()
    {
    }

    public function next()
    {
    }

    public function valid()
    {
    }

    public function offsetExists($offset): bool
    {
        return true;
    }

    public function offsetGet($offset)
    {
    }
    public function offsetSet($offset, $value): void
    {
    }
    public function offsetUnset($offset): void
    {
    }

    public function count()
    {
    }
}
