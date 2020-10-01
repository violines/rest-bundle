<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use TerryApiBundle\Facade\SerializerFacade;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use TerryApiBundle\Event\DeserializeEvent;
use TerryApiBundle\Event\SerializeEvent;
use TerryApiBundle\HttpClient\HttpClient;
use TerryApiBundle\HttpClient\ServerSettings;
use TerryApiBundle\Tests\Stubs\Candy;

class SerializerFacadeTest extends TestCase
{
    /**
     * @Mock
     * @var EventDispatcherInterface
     */
    private \Phake_IMock $eventDispatcher;

    /**
     * @Mock
     * @var Request
     */
    private \Phake_IMock $request;

    /**
     * @Mock
     * @var SerializerInterface
     */
    private \Phake_IMock $serializer;

    public function setUp(): void
    {
        parent::setUp();

        \Phake::initAnnotations($this);
        \Phake::when($this->request)->getLocale->thenReturn('en_GB');

        $this->request->headers = new HeaderBag([
            'Accept' => 'application/pdf, application/json, application/xml',
            'Content-Type' => 'application/json'
        ]);
    }

    public function testShouldSerialize()
    {
        $data = [];
        $context = ['ctxkey' => 'ctxValue'];
        $client = HttpClient::new($this->request, ServerSettings::fromDefaults());

        $serializeContextEvent = new SerializeEvent($data, $client);
        $serializeContextEvent->mergeToContext($context);

        \Phake::when($this->eventDispatcher)->dispatch->thenReturn($serializeContextEvent);
        \Phake::when($this->serializer)->serialize->thenReturn('[]');

        $serializerFacade = new SerializerFacade($this->eventDispatcher, $this->serializer);
        $serializerFacade->serialize($data, $client);

        \Phake::verify($this->serializer)->serialize($data, 'json', $context);
    }

    public function testShouldDeserialize()
    {
        $data = '{"weight": 100, "name": "Bonbon", "tastesGood": true}';
        $type = 'TerryApiBundle\Tests\Stubs\CandyStructStub';
        $context = ['ctxkey' => 'ctxValue'];
        $client = HttpClient::new($this->request, ServerSettings::fromDefaults());

        $deserializeEvent = new DeserializeEvent($data, $client);
        $deserializeEvent->mergeToContext($context);

        \Phake::when($this->eventDispatcher)->dispatch->thenReturn($deserializeEvent);
        \Phake::when($this->serializer)->serialize->thenReturn(new Candy());

        $serializerFacade = new SerializerFacade($this->eventDispatcher, $this->serializer);
        $serializerFacade->deserialize($data, $type, $client);

        \Phake::verify($this->serializer)->deserialize($data, $type, 'json', $context);
    }
}
