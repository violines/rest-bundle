<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Request;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TerryApiBundle\HttpApi\HttpApiReader;
use TerryApiBundle\Request\QueryStringArgumentResolver;
use TerryApiBundle\Request\SupportsException;
use TerryApiBundle\Tests\Mock\Serializer;
use TerryApiBundle\Validation\Validator;

/**
 * @covers \TerryApiBundle\Request\QueryStringArgumentResolver
 */
class QueryStringArgumentResolverTest extends TestCase
{
    /**
     * @Mock
     * @var HttpFoundationRequest
     */
    private \Phake_IMock $request;

    /**
     * @Mock
     * @var ArgumentMetadata
     */
    private \Phake_IMock $argument;

    /**
     * @Mock
     * @var ValidatorInterface
     */
    private \Phake_IMock $validator;

    private QueryStringArgumentResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        \Phake::initAnnotations($this);
        $this->request->headers = new HeaderBag(['Content-Type' => 'application/json']);

        $this->resolver = new QueryStringArgumentResolver(
            new HttpApiReader(new AnnotationReader()),
            new Serializer(),
            new Validator($this->validator)
        );
    }

    /**
     * @dataProvider providerSupportsShouldReturnFalse
     */
    public function testSupportsShouldReturnFalse($type): void
    {
        \Phake::when($this->argument)->getType->thenReturn($type);

        $this->assertFalse($this->resolver->supports($this->request, $this->argument));
    }

    public function providerSupportsShouldReturnFalse(): array
    {
        return [
            ['string'],
            [null],
            [WithoutHttpApi::class],
        ];
    }

    public function testSupportsShouldReturnTrue(): void
    {
        \Phake::when($this->argument)->getType->thenReturn(QueryStringHttpApi::class);

        $this->assertTrue($this->resolver->supports($this->request, $this->argument));
    }

    /**
     * @dataProvider providerResolveShouldThrowException
     */
    public function testResolveShouldThrowException(?string $type): void
    {
        $this->expectException(SupportsException::class);

        \Phake::when($this->argument)->getType->thenReturn($type);

        $result = $this->resolver->resolve($this->request, $this->argument);
        $result->current();
    }

    public function providerResolveShouldThrowException(): array
    {
        return [
            ['string'],
            [null],
        ];
    }

    public function testShouldYield(): void
    {
        \Phake::when($this->argument)->getType->thenReturn(QueryStringHttpApi::class);
        \Phake::when($this->validator)->validate->thenReturn(new ConstraintViolationList());
        $this->request->query = new InputBag(['priceFrom' => 1000, 'priceTo' => 9000]);

        $result = $this->resolver->resolve($this->request, $this->argument);
        $resolved = $result->current();

        $this->assertEquals(1000, $resolved->priceFrom);
        $this->assertEquals(9000, $resolved->priceTo);
    }
}
