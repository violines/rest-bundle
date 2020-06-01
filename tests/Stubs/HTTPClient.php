<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Stubs;

use Symfony\Component\HttpFoundation\Request;
use TerryApiBundle\ValueObject\AbstractHTTPClient;
use TerryApiBundle\ValueObject\HTTPServer;

class HTTPClient extends AbstractHTTPClient
{
    public static function fromRequest(Request $request, HTTPServer $httpServer): self
    {
        return new self($request);
    }

    public function get(string $method)
    {
        return $this->$method();
    }

    public function negotiateProperty(string $subject, string $property, $defaults = [], $availables = [])
    {
        return $this->negotiate($subject, $property, $defaults, $availables);
    }
}
