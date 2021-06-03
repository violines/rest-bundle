<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Type;

use PHPUnit\Framework\TestCase;
use stdClass;
use Violines\RestBundle\Type\ObjectList;
use Violines\RestBundle\Type\TypeException;

/**
 * @covers \Violines\RestBundle\Type\ObjectList
 */
class ObjectListTest extends TestCase
{
    /**
     * @dataProvider providerShouldReturnObjectList
     */
    public function testShouldReturnObjectList(array $expectedArray, array $givenArray): void
    {
        self::assertEquals($expectedArray, ObjectList::fromArray($givenArray)->toArray());
    }

    public function providerShouldReturnObjectList(): array
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
            ],
        ];
    }

    /**
     * @dataProvider providerShouldReturnObjectListsFirstItem
     */
    public function testShouldReturnObjectListsFirstItem(array $givenArray): void
    {
        $list = ObjectList::fromArray($givenArray);

        self::assertEquals(0, $list->first()->number);
    }

    public function providerShouldReturnObjectListsFirstItem()
    {
        return [
            [
                [
                    ListItem::from(0),
                    ListItem::from(1),
                ],
            ],
        ];
    }

    public function testShouldReturnFalseOnFirst(): void
    {
        self::assertFalse(ObjectList::fromArray([])->first());
    }

    /**
     * @dataProvider providerShouldThrowTypeExceptions
     */
    public function testShouldThrowTypeExceptions(array $givenArray): void
    {
        $this->expectException(TypeException::class);

        ObjectList::fromArray($givenArray);
    }

    public function providerShouldThrowTypeExceptions(): array
    {
        return [
            [
                ['test' => 'test'],
            ],
            [
                [
                    new stdClass(),
                    'test' => 'test',
                ],
            ],
            [
                [
                    ListItem::from(0),
                    new stdClass(),
                ],
            ],
        ];
    }
}

class ListItem
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
