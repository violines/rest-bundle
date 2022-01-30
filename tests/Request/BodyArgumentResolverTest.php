<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Request;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
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
use Violines\RestBundle\Tests\Fake\SymfonyEventDispatcherFake;
use Violines\RestBundle\Tests\Fake\SymfonySerializerFake;
use Violines\RestBundle\Tests\Stub\Config;
use Violines\RestBundle\Validation\Validator;

/**
 * @covers \Violines\RestBundle\Request\BodyArgumentResolver
 *
 * @uses \Violines\RestBundle\Serialize\DeserializeEvent
 */
class BodyArgumentResolverTest extends TestCase
{
    use ProphecyTrait;

    private BodyArgumentResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = $this->createResolver($this->prophesize(ValidatorInterface::class)->reveal());
    }

    /**
     * @dataProvider providerSupportsShouldReturnFalse
     */
    public function testSupportsShouldReturnFalse($type, $content, $isNullable): void
    {
        $argument = $this->prophesize(ArgumentMetadata::class);
        $argument->getType()->willReturn($type);
        $argument->isNullable()->willReturn($isNullable);

        $request = $this->createMockRequestWithHeaders();
        $request->getContent()->willReturn($content);

        self::assertFalse($this->resolver->supports($request->reveal(), $argument->reveal()));
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
        $argument = $this->prophesize(ArgumentMetadata::class);
        $argument->getType()->willReturn(DefaultHttpApi::class);
        $argument->isNullable()->willReturn(false);

        $request = $this->createMockRequestWithHeaders();

        self::assertTrue($this->resolver->supports($request->reveal(), $argument->reveal()));
    }

    /**
     * @dataProvider providerResolveShouldThrowException
     */
    public function testResolveShouldThrowException($type, $content, $isNullable): void
    {
        $this->expectException(SupportsException::class);

        $argument = $this->prophesize(ArgumentMetadata::class);
        $argument->getType()->willReturn($type);
        $argument->isNullable()->willReturn($isNullable);

        $request = $this->createMockRequestWithHeaders();
        $request->getContent()->willReturn($content);

        $result = $this->resolver->resolve($request->reveal(), $argument->reveal());
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

        $argument = $this->prophesize(ArgumentMetadata::class);
        $argument->getType()->willReturn(DefaultHttpApi::class);
        $argument->isVariadic()->willReturn(\is_array($expected));

        $request = $this->createMockRequestWithHeaders();
        $request->getContent()->willReturn($content);

        $violationList = new ConstraintViolationList();
        $violationList->add(new ConstraintViolation('test', null, [], null, null, null));

        $validator = $this->prophesize(ValidatorInterface::class);
        $validator->validate($expected)->willReturn($violationList);

        $resolver = $this->createResolver($validator->reveal());

        $result = $resolver->resolve($request->reveal(), $argument->reveal());
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

        $argument = $this->prophesize(ArgumentMetadata::class);
        $argument->getType()->willReturn(DefaultHttpApi::class);
        $argument->isVariadic()->willReturn(\is_array($expected));

        $request = $this->createMockRequestWithHeaders();
        $request->getContent()->willReturn($content);

        $violationList = new ConstraintViolationList();
        $validator = $this->prophesize(ValidatorInterface::class);
        $validator->validate($expected)->willReturn($violationList);

        $resolver = $this->createResolver($validator->reveal());
        $result = $resolver->resolve($request->reveal(), $argument->reveal());

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

        $argument = $this->prophesize(ArgumentMetadata::class);
        $argument->getType()->willReturn(DefaultHttpApi::class);
        $argument->isNullable()->willReturn(false);

        $request = $this->createMockRequestWithHeaders();
        $request->getContent()->willReturn($content);

        $this->resolver->resolve($request->reveal(), $argument->reveal())->current();
    }

    public function providerResolveShouldThrowEmptyBodyException(): array
    {
        return [
            [false],
            [null],
            [''],
        ];
    }

    private function createResolver(ValidatorInterface $validator): BodyArgumentResolver
    {
        return new BodyArgumentResolver(
            new HttpApiReader(new AnnotationReader()),
            new Serializer(new SymfonyEventDispatcherFake(), new SymfonySerializerFake(), new FormatMapper(Config::SERIALIZE_FORMATS)),
            new Validator($validator)
        );
    }

    private function createMockRequestWithHeaders()
    {
        $request = $this->prophesize(HttpFoundationRequest::class);

        $request->headers = new HeaderBag(['Content-Type' => 'application/json']);

        return $request;
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
