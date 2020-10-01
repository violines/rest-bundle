<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Error;

use TerryApiBundle\Error\ErrorInterface;

class ErrorException extends \LogicException implements \Throwable, ErrorInterface
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
