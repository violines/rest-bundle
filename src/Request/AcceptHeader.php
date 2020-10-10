<?php

declare(strict_types=1);

namespace TerryApiBundle\Request;

use TerryApiBundle\Negotiation\ContentNegotiator;
use TerryApiBundle\Negotiation\Negotiatable;
use TerryApiBundle\Serialize\Format;

final class AcceptHeader implements Negotiatable
{
    public const NAME = 'Accept';
    private string $accept;

    private function __construct(string $accept)
    {
        $this->accept = $accept;
    }

    public static function fromString(string $accept): self
    {
        return new self($accept);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getValue(): string
    {
        return $this->accept;
    }

    public function toFormat(ContentNegotiator $negotiator): Format
    {
        return $negotiator->negotiate($this);
    }
}
