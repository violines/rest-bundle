<?php

declare(strict_types=1);

namespace TerryApiBundle\Negotiation;

interface Negotiatable
{
    public function getName(): string;

    public function getValue(): string;
}
