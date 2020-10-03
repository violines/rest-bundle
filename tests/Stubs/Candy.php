<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Stubs;

use TerryApiBundle\HttpApi\HttpApi;
use Symfony\Component\Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @HttpApi
 * @FakeAnnotation(fakeBool=true)
 */
class Candy
{
    /**
     * @Serializer\Annotation\SerializedName("weight")
     * @Assert\Positive
     */
    public int $weight = 100;

    /**
     * @Serializer\Annotation\SerializedName("name")
     */
    public string $name = 'Bonbon';

    /**
     * @Serializer\Annotation\SerializedName("tastes_good")
     */
    public bool $tastesGood = true;
}
