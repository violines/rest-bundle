<?php

declare(strict_types=1);

namespace Violines\RestBundle\Error;

interface ErrorInterface
{
    /**
     * must return an Object with Violines\RestBundle\HttpApi\HttpApi attribute or annotation.
     */
    public function getContent(): object;

    /**
     * use the consts from Symfony\Component\HttpFoundation\Response.
     */
    public function getStatusCode(): int;
}
