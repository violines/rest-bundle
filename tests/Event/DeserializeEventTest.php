<?php

declare(strict_types=1);

namespace TerryApi\Tests\Event;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use TerryApiBundle\Event\DeserializeEvent;
use TerryApiBundle\HttpClient\HttpClient;
use TerryApiBundle\HttpClient\ServerSettings;

class DeserializeEventTest extends TestCase
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
        $data = '{"weight": 100, "name": "Bonbon", "tastesGood": true}';
        $client = HttpClient::new($this->request, ServerSettings::fromDefaults());
        $context = ['firstKey' => 'firstVal', 'secondkey' => 'secondVal'];

        $serializeContextEvent = new DeserializeEvent($data, $client);

        $serializeContextEvent->mergeToContext(['firstKey' => 'firstVal']);
        $serializeContextEvent->mergeToContext(['secondkey' => 'secondVal']);

        $this->assertEquals($data, $serializeContextEvent->getData());
        $this->assertEquals($client, $serializeContextEvent->getHttpClient());
        $this->assertEquals($context, $serializeContextEvent->getContext());
    }
}
