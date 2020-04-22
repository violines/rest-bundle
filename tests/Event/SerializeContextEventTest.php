<?php

declare(strict_types=1);

namespace TerryApi\Tests\Event;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use TerryApiBundle\Event\SerializeContextEvent;
use TerryApiBundle\ValueObject\HTTPClient;
use TerryApiBundle\ValueObject\HTTPServer;

class SerializeContextEventTest extends TestCase
{
    /**
     * @Mock
     * @var Request
     */
    private \Phake_IMock $request;

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

    public function testShouldCreateEvent()
    {
        $data = [];
        $client = HTTPClient::fromRequest($this->request, new HTTPServer());
        $context = ['firstKey' => 'firstVal', 'secondkey' => 'secondVal'];

        $serializeContextEvent = new SerializeContextEvent($data, $client);

        $serializeContextEvent->mergeToContext(['firstKey' => 'firstVal']);
        $serializeContextEvent->mergeToContext(['secondkey' => 'secondVal']);

        $this->assertEquals($data, $serializeContextEvent->getData());
        $this->assertEquals($client, $serializeContextEvent->getHttpClient());
        $this->assertEquals($context, $serializeContextEvent->getContext());
    }
}
