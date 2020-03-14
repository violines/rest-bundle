<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Stubs;

use Symfony\Component\HttpFoundation\Request;
use TerryApiBundle\ValueObject\AbstractClient;
use TerryApiBundle\ValueObject\HTTPServerDefaults;

class ClientStub extends AbstractClient
{
    public static function fromRequest(Request $request, HTTPServerDefaults $httpServerDefaults): self
    {
        return new self($request->headers);
    }

    public function get(string $property)
    {
        return $this->$property;
    }

    public function negotiateProperty(string $subject, string $property, $defaults = [], $availables = [])
    {
        return $this->negotiate($subject, $property, $defaults, $availables);
    }
}
