<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Request;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TerryApiBundle\Error\ValidationException;
use TerryApiBundle\HttpApi\HttpApiReader;
use TerryApiBundle\Request\BodyArgumentResolver;
use TerryApiBundle\Request\EmptyBodyException;
use TerryApiBundle\Request\SupportsException;
use TerryApiBundle\Serialize\FormatMapper;
use TerryApiBundle\Serialize\Serializer;
use TerryApiBundle\Tests\Mock\Dispatcher;
use TerryApiBundle\Tests\Mock\Serializer as MockSerializer;
use TerryApiBundle\Tests\Stubs\Config;
use TerryApiBundle\Validation\Validator;

/**
 * @covers \TerryApiBundle\Request\BodyArgumentResolver
 * @uses \TerryApiBundle\Serialize\DeserializeEvent
 */
class BodyArgumentResolverTest extends TestCase
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

    private BodyArgumentResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        \Phake::initAnnotations($this);
        $this->request->headers = new HeaderBag(['Content-Type' => 'application/json']);

        $this->resolver = new BodyArgumentResolver(
            new HttpApiReader(new AnnotationReader()),
            new Serializer(new Dispatcher(), new MockSerializer(), new FormatMapper(Config::SERIALIZE_FORMATS)),
            new Validator($this->validator)
        );
    }

    /**
     * @dataProvider providerSupportsShouldReturnFalse
     */
    public function testSupportsShouldReturnFalse($type, $content, $isNullable): void
    {
        \Phake::when($this->argument)->getType->thenReturn($type);
        \Phake::when($this->request)->getContent->thenReturn($content);
        \Phake::when($this->argument)->isNullable->thenReturn($isNullable);

        $this->assertFalse($this->resolver->supports($this->request, $this->argument));
    }

    public function providerSupportsShouldReturnFalse(): array
    {
        return [
            ['string', '{}', false],
            [null, '{}', false],
            [WithoutHttpApi::class, '{}', false],
            [DefaultHttpApi::class, false, true],
            [DefaultHttpApi::class, null, true],
            [DefaultHttpApi::class, '', true],
        ];
    }

    public function testSupportsShouldReturnTrue(): void
    {
        \Phake::when($this->argument)->getType->thenReturn(DefaultHttpApi::class);

        $this->assertTrue($this->resolver->supports($this->request, $this->argument));
    }

    /**
     * @dataProvider providerResolveShouldThrowException
     */
    public function testResolveShouldThrowException($type, $content, $isNullable): void
    {
        $this->expectException(SupportsException::class);

        \Phake::when($this->argument)->getType->thenReturn($type);
        \Phake::when($this->request)->getContent->thenReturn($content);
        \Phake::when($this->argument)->isNullable->thenReturn($isNullable);

        $result = $this->resolver->resolve($this->request, $this->argument);
        $result->current();
    }

    public function providerResolveShouldThrowException(): array
    {
        return [
            ['string', '{}', false],
            [null, '{}', false],
            [DefaultHttpApi::class, false, true],
            [DefaultHttpApi::class, null, true],
            [DefaultHttpApi::class, '', true],
        ];
    }

    /**
     * @dataProvider providerResolveShouldThrowValidationException
     */
    public function testResolveShouldThrowValidationException($expected): void
    {
        $this->expectException(ValidationException::class);

        $content = json_encode($expected);

        \Phake::when($this->request)->getContent->thenReturn($content);
        \Phake::when($this->argument)->isVariadic->thenReturn(is_array($expected));
        \Phake::when($this->argument)->getType->thenReturn(DefaultHttpApi::class);

        $violationList = new ConstraintViolationList();
        $violationList->add(new ConstraintViolation('test', null, [], null, null, null));
        \Phake::when($this->validator)->validate->thenReturn($violationList);

        $result = $this->resolver->resolve($this->request, $this->argument);
        $result->current();
    }

    public function providerResolveShouldThrowValidationException(): array
    {
        return [
            [
                [new DefaultHttpApi(), new DefaultHttpApi()],
            ],
            [
                new DefaultHttpApi(),
            ]
        ];
    }

    /**
     * @dataProvider providerResolveShouldYield
     */
    public function testResolveShouldYield($expected): void
    {
        $content = json_encode($expected);

        \Phake::when($this->request)->getContent->thenReturn($content);
        \Phake::when($this->argument)->isVariadic->thenReturn(is_array($expected));
        \Phake::when($this->argument)->getType->thenReturn(DefaultHttpApi::class);
        \Phake::when($this->validator)->validate->thenReturn(new ConstraintViolationList());

        $result = $this->resolver->resolve($this->request, $this->argument);
        $this->assertInstanceOf(DefaultHttpApi::class, $result->current());
    }

    public function providerResolveShouldYield(): array
    {
        return [
            [
                [new DefaultHttpApi(), new DefaultHttpApi()],
            ],
            [
                new DefaultHttpApi(),
            ]
        ];
    }

    /**
     * @dataProvider providerResolveShouldThrowEmptyBodyException
     */
    public function testResolveShouldThrowEmptyBodyException($content): void
    {
        $this->expectException(EmptyBodyException::class);

        \Phake::when($this->argument)->getType->thenReturn(DefaultHttpApi::class);
        \Phake::when($this->request)->getContent->thenReturn($content);
        \Phake::when($this->argument)->isNullable->thenReturn(false);

        $this->resolver->resolve($this->request, $this->argument)->current();
    }

    public function providerResolveShouldThrowEmptyBodyException(): array
    {
        return [
            [false],
            [null],
            [''],
        ];
    }
}
