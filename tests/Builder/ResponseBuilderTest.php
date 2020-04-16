<?php

declare(strict_types=1);

namespace TerryApi\Tests\Builder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use TerryApiBundle\Builder\ResponseBuilder;

class ResponseBuilderTest extends TestCase
{
    private ResponseBuilder $responseBuilder;

    public function setUp(): void
    {
        $this->responseBuilder = new ResponseBuilder();
    }

    public function testShouldReturnEmptyResponse()
    {
        $this->assertInstanceOf(Response::class, $this->responseBuilder->getResponse());
    }

    public function testShouldReturnResponseWithContent()
    {
        $content = '{"text": "i am a string"}';

        $response = $this->responseBuilder->setContent($content)->getResponse();

        $this->assertEquals($content, $response->getContent());
    }

    /**
     * @dataProvider providerShouldResponseWithCustomStatusCode
     */
    public function testShouldResponseWithCustomStatusCode(int $status)
    {
        $response = $this->responseBuilder->setStatus($status)->getResponse();

        $this->assertEquals($status, $response->getStatusCode());
    }

    public function providerShouldResponseWithCustomStatusCode()
    {
        return [
            [Response::HTTP_CREATED]
        ];
    }

    public function testShouldResponseWithHeaders()
    {
        $headers = ['content-type' => 'application/json'];

        $response = $this->responseBuilder->setHeaders($headers)->getResponse();

        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }
}
