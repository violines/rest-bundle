<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Type;

use \stdClass;
use PHPUnit\Framework\TestCase;
use Violines\RestBundle\Type\ObjectCollection;
use Violines\RestBundle\Type\TypeException;

/**
 * @covers \Violines\RestBundle\Type\ObjectCollection
 */
class ObjectCollectionTest extends TestCase
{
    /**
     * @dataProvider providerShouldReturnObjectCollection
     */
    public function testShouldReturnObjectCollection(array $expectedArray, array $givenArray): void
    {
        self::assertEquals($expectedArray, ObjectCollection::fromArray($givenArray)->toArray());
    }

    public function providerShouldReturnObjectCollection(): array
    {
        return [
            [
                [],
                [],
            ],
            [
                [
                    new stdClass(),
                    new stdClass(),
                ],
                [
                    new stdClass(),
                    new stdClass(),
                ],
            ],
            [
                [
                    new stdClass(),
                    new stdClass(),
                ],
                [
                    'a' => new stdClass(),
                    'b' => new stdClass(),
                ],
            ]
        ];
    }

    /**
     * @dataProvider providerShouldReturnObjectCollectionsFirstItem
     */
    public function testShouldReturnObjectCollectionsFirstItem(array $givenArray): void
    {
        $collection = ObjectCollection::fromArray($givenArray);

        self::assertEquals(0, $collection->first()->number);
    }

    public function providerShouldReturnObjectCollectionsFirstItem()
    {
        return [
            [
                [
                    CollectionItem::from(0),
                    CollectionItem::from(1),
                ]
            ]
        ];
    }

    public function testShouldReturnFalseOnFirst(): void
    {
        self::assertFalse(ObjectCollection::fromArray([])->first());
    }


    /**
     * @dataProvider providerShouldThrowTypeExceptions
     */
    public function testShouldThrowTypeExceptions(array $givenArray): void
    {
        $this->expectException(TypeException::class);

        ObjectCollection::fromArray($givenArray);
    }

    public function providerShouldThrowTypeExceptions(): array
    {
        return [
            [
                ['test' => 'test']
            ],
            [
                [
                    new stdClass(),
                    'test' => 'test',
                ]
            ],
            [
                [
                    CollectionItem::from(0),
                    new stdClass()
                ]
            ]
        ];
    }
}

class CollectionItem
{
    public $number;

    private function __construct(int $number)
    {
        $this->number = $number;
    }

    public static function from(int $number): self
    {
        return new self($number);
    }
}
