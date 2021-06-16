<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Request;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Violines\RestBundle\HttpApi\HttpApi;
use Violines\RestBundle\HttpApi\HttpApiReader;
use Violines\RestBundle\Request\QueryStringArgumentResolver;
use Violines\RestBundle\Request\SupportsException;
use Violines\RestBundle\Tests\Fake\SymfonySerializerFake;
use Violines\RestBundle\Validation\Validator;

/**
 * @covers \Violines\RestBundle\Request\QueryStringArgumentResolver
 */
class QueryStringArgumentResolverTest extends TestCase
{
    use ProphecyTrait;

    private QueryStringArgumentResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $validator = $this->prophesize(ValidatorInterface::class);
        $validator->validate(Argument::any())->willReturn(new ConstraintViolationList());

        $this->resolver = new QueryStringArgumentResolver(
            new HttpApiReader(new AnnotationReader()),
            new SymfonySerializerFake(),
            new Validator($validator->reveal())
        );
    }

    /**
     * @dataProvider providerSupportsShouldReturnFalse
     */
    public function testSupportsShouldReturnFalse($type, array $query, bool $isNullable): void
    {
        $request = $this->prophesize(HttpFoundationRequest::class);
        $request->query = new ParameterBag($query);

        $argument = $this->prophesize(ArgumentMetadata::class);
        $argument->getType()->willReturn($type);
        $argument->isNullable()->willReturn($isNullable);

        self::assertFalse($this->resolver->supports($request->reveal(), $argument->reveal()));
    }

    public function providerSupportsShouldReturnFalse(): \Generator
    {
        yield ['string', ['param1' => 'value1'], false];
        yield [null, ['param1' => 'value1'], false];
        yield [WithoutHttpApi::class, ['param1' => 'value1'], false];
        yield [QueryStringHttpApi::class, [], true];
    }

    /**
     * @dataProvider providerSupportsShouldReturnTrue
     */
    public function testSupportsShouldReturnTrue(array $query, bool $isNullable): void
    {
        $request = $this->prophesize(HttpFoundationRequest::class);
        $request->query = new ParameterBag($query);

        $argument = $this->prophesize(ArgumentMetadata::class);
        $argument->getType()->willReturn(QueryStringHttpApi::class);
        $argument->isNullable()->willReturn($isNullable);

        self::assertTrue($this->resolver->supports($request->reveal(), $argument->reveal()));
    }

    public function providerSupportsShouldReturnTrue(): \Generator
    {
        yield [[], false];
        yield [['param1' => 'value1'], true];
    }

    /**
     * @dataProvider providerResolveShouldThrowException
     */
    public function testResolveShouldThrowException(?string $type): void
    {
        $this->expectException(SupportsException::class);

        $request = $this->prophesize(HttpFoundationRequest::class);

        $argument = $this->prophesize(ArgumentMetadata::class);
        $argument->getType()->willReturn($type);

        $result = $this->resolver->resolve($request->reveal(), $argument->reveal());
        $result->current();
    }

    public function providerResolveShouldThrowException(): \Generator
    {
        yield ['string'];
        yield [null];
    }

    public function testShouldYield(): void
    {
        $request = $this->prophesize(HttpFoundationRequest::class);
        $request->query = new ParameterBag(['priceFrom' => 1000, 'priceTo' => 9000]);

        $argument = $this->prophesize(ArgumentMetadata::class);
        $argument->getType()->willReturn(QueryStringHttpApi::class);

        $result = $this->resolver->resolve($request->reveal(), $argument->reveal());
        $resolved = $result->current();

        self::assertEquals(1000, $resolved->priceFrom);
        self::assertEquals(9000, $resolved->priceTo);
    }
}

/**
 * @HttpApi(requestInfoSource=HttpApi::QUERY_STRING)
 */
class QueryStringHttpApi
{
    public $priceFrom;
    public $priceTo;
}

class WithoutHttpApi
{
}
