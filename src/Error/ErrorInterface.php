<?php

declare(strict_types=1);

namespace TerryApiBundle\Error;

interface ErrorInterface
{
    /**
     * must return an Object with TerryApiBundle\HttpApi\HttpApi annotation
     */
    public function getContent(): object;

    /**
     * use the consts from Symfony\Component\HttpFoundation\Response
     */
    public function getStatusCode(): int;
}
