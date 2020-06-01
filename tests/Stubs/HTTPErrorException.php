<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Stubs;

use TerryApiBundle\Exception\HTTPErrorInterface;

class HTTPErrorException extends \LogicException implements \Throwable, HTTPErrorInterface
{
    private $content;

    public function getContent(): object
    {
        return $this->content;
    }

    public function getHTTPStatusCode(): int
    {
        return 400;
    }

    public function setContent(object $content): void
    {
        $this->content = $content;
    }
}
