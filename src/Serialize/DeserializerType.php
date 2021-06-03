<?php

declare(strict_types=1);

namespace Violines\RestBundle\Serialize;

/**
 * @internal
 */
final class DeserializerType
{
    private string $type;

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param class-string $fqcn
     */
    public static function object(string $fqcn): self
    {
        return new self($fqcn);
    }

    /**
     * @param class-string $fqcn
     */
    public static function array(string $fqcn): self
    {
        return new self($fqcn . '[]');
    }

    public function toString(): string
    {
        return $this->type;
    }
}
