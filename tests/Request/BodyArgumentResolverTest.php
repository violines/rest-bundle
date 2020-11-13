<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Request;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use TerryApiBundle\Error\ValidationException;
use TerryApiBundle\HttpApi\HttpApiReader;
use TerryApiBundle\Request\BodyArgumentResolver;
use TerryApiBundle\Request\SupportsException;
use TerryApiBundle\Serialize\DeserializeEvent;
use TerryApiBundle\Serialize\FormatMapper;
use TerryApiBundle\Serialize\Serializer;
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
     * @var EventDispatcherInterface
     */
    private \Phake_IMock $eventDispatcher;

    /**
     * @Mock
     * @var SerializerInterface
     */
    private \Phake_IMock $serializerInterface;

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

    public function setUp(): void
    {
        parent::setUp();
        \Phake::initAnnotations($this);
        $this->request->headers = new HeaderBag(['Content-Type' => 'application/json']);

        $this->resolver = new BodyArgumentResolver(
            new HttpApiReader(new AnnotationReader()),
            new Serializer($this->eventDispatcher, $this->serializerInterface, new FormatMapper(Config::SERIALIZE_FORMATS)),
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
        \Phake::when($this->argument)->getType->thenReturn(DefaultHttpApi::class);

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
        \Phake::when($this->serializerInterface)->deserialize->thenReturn($expected);
        \Phake::when($this->eventDispatcher)->dispatch->thenReturn(new DeserializeEvent($content, 'json'));

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
        \Phake::when($this->serializerInterface)->deserialize->thenReturn($expected);
        \Phake::when($this->validator)->validate->thenReturn(new ConstraintViolationList());
        \Phake::when($this->eventDispatcher)->dispatch->thenReturn(new DeserializeEvent($content, 'json'));

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
     * @dataProvider providerResolveShouldTypeCastEmpty
     */
    public function testResolveShouldTypeCastEmpty($content): void
    {
        \Phake::when($this->request)->getContent->thenReturn($content);
        \Phake::when($this->argument)->isVariadic->thenReturn(false);
        \Phake::when($this->argument)->getType->thenReturn(DefaultHttpApi::class);
        \Phake::when($this->validator)->validate->thenReturn(new ConstraintViolationList());
        \Phake::when($this->eventDispatcher)->dispatch->thenReturn(new DeserializeEvent((string)$content, 'json'));

        $result = $this->resolver->resolve($this->request, $this->argument);
        $result->current();

        \Phake::verify($this->serializerInterface)->deserialize('', \Phake::ignoreRemaining());
    }

    public function providerResolveShouldTypeCastEmpty(): array
    {
        return [
            [false],
            [null],
        ];
    }
}
