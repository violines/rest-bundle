<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Request;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Violines\RestBundle\HttpApi\HttpApi;
use Violines\RestBundle\HttpApi\HttpApiReader;
use Violines\RestBundle\Request\QueryStringArgumentResolver;
use Violines\RestBundle\Request\SupportsException;
use Violines\RestBundle\Tests\Mock\Serializer;
use Violines\RestBundle\Validation\Validator;

/**
 * @covers \Violines\RestBundle\Request\QueryStringArgumentResolver
 */
class QueryStringArgumentResolverTest extends TestCase
{
    /**
     * @Mock
     *
     * @var HttpFoundationRequest
     */
    private \Phake_IMock $request;

    /**
     * @Mock
     *
     * @var ArgumentMetadata
     */
    private \Phake_IMock $argument;

    /**
     * @Mock
     *
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
        $this->request->query = new ParameterBag(['priceFrom' => 1000, 'priceTo' => 9000]);

        $result = $this->resolver->resolve($this->request, $this->argument);
        $resolved = $result->current();

        $this->assertEquals(1000, $resolved->priceFrom);
        $this->assertEquals(9000, $resolved->priceTo);
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
