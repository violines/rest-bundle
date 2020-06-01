<?php

namespace TerryApiBundle\Exception;

interface HTTPErrorInterface
{
    /**
     * must return an Object with TerryApiBundle\Annotation\HTTPApi annotation
     */
    public function getContent(): object;

    /**
     * use the consts from Symfony\Component\HttpFoundation\Response
     */
    public function getHTTPStatusCode(): int;
}
