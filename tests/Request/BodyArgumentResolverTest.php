<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Request;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Violines\RestBundle\Error\ValidationException;
use Violines\RestBundle\HttpApi\HttpApi;
use Violines\RestBundle\HttpApi\HttpApiReader;
use Violines\RestBundle\Request\BodyArgumentResolver;
use Violines\RestBundle\Request\EmptyBodyException;
use Violines\RestBundle\Request\SupportsException;
use Violines\RestBundle\Serialize\FormatMapper;
use Violines\RestBundle\Serialize\Serializer;
use Violines\RestBundle\Tests\Mock\Dispatcher;
use Violines\RestBundle\Tests\Mock\Serializer as MockSerializer;
use Violines\RestBundle\Tests\Stubs\Config;
use Violines\RestBundle\Validation\Validator;

/**
 * @covers \Violines\RestBundle\Request\BodyArgumentResolver
 *
 * @uses \Violines\RestBundle\Serialize\DeserializeEvent
 */
class BodyArgumentResolverTest extends TestCase
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

        $content = \json_encode($expected);

        \Phake::when($this->request)->getContent->thenReturn($content);
        \Phake::when($this->argument)->isVariadic->thenReturn(\is_array($expected));
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
            ],
        ];
    }

    /**
     * @dataProvider providerResolveShouldYield
     */
    public function testResolveShouldYield($expected): void
    {
        $content = \json_encode($expected);

        \Phake::when($this->request)->getContent->thenReturn($content);
        \Phake::when($this->argument)->isVariadic->thenReturn(\is_array($expected));
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
            ],
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

/**
 * @HttpApi
 */
class DefaultHttpApi
{
    public int $int = 1;
    public string $name = 'name';
    public bool $is_true = true;
}
