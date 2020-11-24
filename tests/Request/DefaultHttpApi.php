<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Request;

use Symfony\Component\Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use TerryApiBundle\HttpApi\HttpApi;

/**
 * @HttpApi
 */
class DefaultHttpApi
{
    /**
     * @Serializer\Annotation\SerializedName("int")
     * @Assert\Positive
     */
    public int $int = 1;

    /**
     * @Serializer\Annotation\SerializedName("name")
     */
    public string $name = 'name';

    /**
     * @Serializer\Annotation\SerializedName("is_true")
     */
    public bool $isTrue = true;
}
