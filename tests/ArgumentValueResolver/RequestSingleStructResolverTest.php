<?php

declare(strict_types=1);

namespace TerryApi\Tests\ArgumentValueResolver;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TerryApiBundle\Annotation\Struct;
use TerryApiBundle\Annotation\StructReader;
use TerryApiBundle\ArgumentValueResolver\RequestSingleStructResolver;
use TerryApiBundle\Exception\AnnotationNotFoundException;
use TerryApiBundle\Exception\ValidationException;
use TerryApiBundle\Tests\Stubs\CandyStructStub;

class RequestSingleStructResolverTest extends TestCase
{
    /**
     * @Mock
     * @var ArgumentMetadata
     */
    private \Phake_IMock $argument;

    /**
     * @Mock
     * @var HttpFoundationRequest
     */
    private \Phake_IMock $request;

    /**
     * @Mock
     * @var SerializerInterface
     */
    private \Phake_IMock $serializer;

    /**
     * @Mock
     * @var StructReader
     */
    private \Phake_IMock $structReader;

    /**
     * @Mock
     * @var ValidatorInterface
     */
    private \Phake_IMock $validator;

    private RequestSingleStructResolver $resolver;

    public function setUp(): void
    {
        parent::setUp();

        \Phake::initAnnotations($this);

        $this->resolver = new RequestSingleStructResolver(
            $this->serializer,
            $this->structReader,
            $this->validator
        );
    }

    /**
     * @dataProvider providerSupportsShouldReturnFalse
     */
    public function testSupportsShouldReturnFalse(
        ?string $type,
        ?string $content,
        bool $isVariadic,
        bool $throwException
    ) {
        \Phake::when($this->argument)->getType->thenReturn($type);
        \Phake::when($this->request)->getContent->thenReturn($content);
        \Phake::when($this->argument)->isVariadic->thenReturn($isVariadic);

        if ($throwException) {
            \Phake::when($this->structReader)->read->thenThrow(new AnnotationNotFoundException());
        }

        $supports = $this->resolver->supports($this->request, $this->argument);

        $this->assertFalse($supports);
    }

    public function providerSupportsShouldReturnFalse(): array
    {
        return [
            ['string', 'this is a string', false, false],
            [null, 'this is a string', false, false],
            [CandyStructStub::class, null, false, false],
            [CandyStructStub::class, 'this is a string', false, true],
            [CandyStructStub::class, 'this is a string', true, false],
        ];
    }

    public function testSupportsShouldReturnTrue()
    {
        \Phake::when($this->request)->getContent->thenReturn('this is a string');
        \Phake::when($this->argument)->getType->thenReturn(CandyStructStub::class);

        $structAnnotation = new Struct();
        $structAnnotation->supports = true;
        \Phake::when($this->structReader)->read->thenReturn($structAnnotation);

        $supports = $this->resolver->supports($this->request, $this->argument);

        $this->assertTrue($supports);
    }

    /**
     * @dataProvider providerResolveShouldThrowException
     */
    public function testResolveShouldThrowException(?string $type, ?string $content)
    {
        $this->request->headers = \Phake::mock(ParameterBag::class);
        \Phake::when($this->request)->getContent->thenReturn($content);
        \Phake::when($this->request->headers)->get->thenReturn('application/json');
        \Phake::when($this->argument)->getType->thenReturn($type);

        $this->expectException(\LogicException::class);

        $result = $this->resolver->resolve($this->request, $this->argument);

        foreach ($result as $item) {
        }
    }

    public function providerResolveShouldThrowException(): array
    {
        return [
            ['string', 'this is a string'],
            [null, 'this is a string'],
            [CandyStructStub::class, null],
        ];
    }

    /**
     * @dataProvider providerResolveShouldYield
     */
    public function testResolveShouldThrowValidationException()
    {
        $this->expectException(ValidationException::class);

        $candy = new CandyStructStub();

        $this->request->headers = \Phake::mock(ParameterBag::class);
        \Phake::when($this->request)->getContent->thenReturn(json_encode($candy));
        \Phake::when($this->request->headers)->get->thenReturn('application/json');
        \Phake::when($this->argument)->isVariadic->thenReturn(false);
        \Phake::when($this->argument)->getType->thenReturn(CandyStructStub::class);
        \Phake::when($this->serializer)->deserialize->thenReturn($candy);

        $violationList = new ConstraintViolationList();
        $violationList->add(new ConstraintViolation('test', null, [], null, null, null));
        \Phake::when($this->validator)->validate->thenReturn($violationList);

        $result = $this->resolver->resolve($this->request, $this->argument);

        foreach ($result as $item) {
        }
    }

    /**
     * @dataProvider providerResolveShouldYield
     */
    public function testResolveShouldYield($isVariadic, $expected, $expectedClassName)
    {
        $this->request->headers = \Phake::mock(ParameterBag::class);
        \Phake::when($this->request)->getContent->thenReturn(json_encode($expected));
        \Phake::when($this->request->headers)->get->thenReturn('application/json');
        \Phake::when($this->argument)->isVariadic->thenReturn($isVariadic);
        \Phake::when($this->argument)->getType->thenReturn($expectedClassName);
        \Phake::when($this->serializer)->deserialize->thenReturn($expected);
        \Phake::when($this->validator)->validate->thenReturn(new ConstraintViolationList());

        $result = $this->resolver->resolve($this->request, $this->argument);

        $count = 0;

        foreach ($result as $generatorObject) {
            ++$count;
            $this->assertInstanceOf($expectedClassName, $generatorObject);
        }

        // makes sure, that generator does not yield nothing
        $this->assertGreaterThan(0, $count);
    }

    public function providerResolveShouldYield(): array
    {
        return [
            [
                false,
                new CandyStructStub(),
                CandyStructStub::class
            ],
        ];
    }
}
