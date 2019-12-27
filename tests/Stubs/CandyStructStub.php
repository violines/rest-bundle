<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Stubs;

use TerryApiBundle\Annotation\Struct;
use Symfony\Component\Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Struct(supports=true)
 * @FakeAnnotation(fakeBool=true)
 */
class CandyStructStub
{
    /**
     * @Serializer\Annotation\SerializedName("weight")
     * @Assert\Positive
     */
    public int $weight;

    /**
     * @Serializer\Annotation\SerializedName("name")
     */
    public string $name;

    /**
     * @Serializer\Annotation\SerializedName("tastes_good")
     */
    public bool $tastesGood;
}
