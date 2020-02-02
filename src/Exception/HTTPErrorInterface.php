<?php

namespace TerryApiBundle\Exception;

interface HTTPErrorInterface
{
    /**
     * must return an Object with TerryApiBundle\Annotation\Struct annotation
     */
    public function getStruct(): object;

    /**
     * use the consts from Symfony\Component\HttpFoundation\Response
     */
    public function getHTTPStatusCode(): int;
}
