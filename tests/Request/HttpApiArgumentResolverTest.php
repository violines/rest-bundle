<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Request;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use TerryApiBundle\Error\ValidationException;
use TerryApiBundle\HttpApi\HttpApi;
use TerryApiBundle\HttpApi\HttpApiReader;
use TerryApiBundle\Request\HttpApiArgumentResolver;
use TerryApiBundle\Serialize\DeserializeEvent;
use TerryApiBundle\Serialize\FormatMapper;
use TerryApiBundle\Serialize\Serializer;
use TerryApiBundle\Tests\Stubs\Candy;
use TerryApiBundle\Tests\Stubs\Config;
use TerryApiBundle\Tests\Stubs\Gum;

class HttpApiArgumentResolverTest extends TestCase
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
    private \Phake_IMock $serializer;

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

    private HttpApiArgumentResolver $resolver;

    public function setUp(): void
    {
        parent::setUp();
        \Phake::initAnnotations($this);

        $this->request->headers = new HeaderBag(['Content-Type' => 'application/json']);

        $this->resolver = new HttpApiArgumentResolver(
            new HttpApiReader(new AnnotationReader()),
            new ObjectNormalizer(),
            new Serializer($this->eventDispatcher, $this->serializer, new FormatMapper(Config::SERIALIZE_FORMATS)),
            $this->validator
        );
    }

    /**
     * @dataProvider providerSupportsShouldReturnFalse
     */
    public function testSupportsShouldReturnFalse(?string $type)
    {
        \Phake::when($this->argument)->getType->thenReturn($type);

        $supports = $this->resolver->supports($this->request, $this->argument);

        $this->assertFalse($supports);
    }

    public function providerSupportsShouldReturnFalse(): array
    {
        return [
            ['string'],
            [null],
            [Gum::class],
        ];
    }

    public function testSupportsShouldReturnTrue()
    {
        \Phake::when($this->argument)->getType->thenReturn(Candy::class);

        $supports = $this->resolver->supports($this->request, $this->argument);

        $this->assertTrue($supports);
    }

    /**
     * @dataProvider providerResolveShouldThrowException
     */
    public function testResolveShouldThrowException(?string $type)
    {
        \Phake::when($this->argument)->getType->thenReturn($type);

        $this->expectException(\LogicException::class);

        $result = $this->resolver->resolve($this->request, $this->argument);
        $result->current();
    }

    public function providerResolveShouldThrowException(): array
    {
        return [
            ['string'],
            [null]
        ];
    }

    /**
     * @dataProvider providerResolveShouldThrowValidationException
     */
    public function testResolveShouldThrowValidationException($expected)
    {
        $this->expectException(ValidationException::class);

        $content = json_encode($expected);

        \Phake::when($this->request)->getContent->thenReturn($content);
        \Phake::when($this->argument)->isVariadic->thenReturn(is_array($expected));
        \Phake::when($this->argument)->getType->thenReturn(Candy::class);
        \Phake::when($this->serializer)->deserialize->thenReturn($expected);
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
                [new Candy(), new Candy()],
            ],
            [
                new Candy(),
            ]
        ];
    }

    /**
     * @dataProvider providerResolveShouldYield
     */
    public function testResolveShouldYield($expected, $type, $data, $requestInfoSource)
    {
        \Phake::when($this->argument)->getType->thenReturn($type);

        if (HttpApi::QUERY_STRING === $requestInfoSource) {
            $this->request->query = new InputBag($data);
        }

        if (HttpApi::BODY === $requestInfoSource) {
            \Phake::when($this->request)->getContent->thenReturn($data);
            \Phake::when($this->argument)->isVariadic->thenReturn(is_array($expected));
            \Phake::when($this->serializer)->deserialize->thenReturn($expected);
            \Phake::when($this->eventDispatcher)->dispatch->thenReturn(new DeserializeEvent($data, 'json'));
        }
        
        \Phake::when($this->validator)->validate->thenReturn(new ConstraintViolationList());
        
        $result = $this->resolver->resolve($this->request, $this->argument);
        $this->assertInstanceOf($type, $result->current());
    }

    public function providerResolveShouldYield(): array
    {
        return [
            [
                new QueryString(),
                QueryString::class,
                ['filterPriceFrom' => 1000, 'filterPriceTo' => 9000],
                HttpApi::QUERY_STRING
            ],
            [
                [new Candy(), new Candy()],
                Candy::class,
                '[{"weight":100,"name":"Bonbon","tastesGood":true},{"weight":100,"name":"Bonbon","tastesGood":true}]',
                HttpApi::BODY
            ],
            [
                new Candy(),
                Candy::class,
                '{"weight":100,"name":"Bonbon","tastesGood":true}',
                HttpApi::BODY
            ]
        ];
    }
}
