<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Response;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Violines\RestBundle\Response\ContentTypeHeader;
use Violines\RestBundle\Response\ResponseBuilder;

/**
 * @covers \Violines\RestBundle\Response\ResponseBuilder
 *
 * @uses \Violines\RestBundle\Response\ContentTypeHeader
 */
class ResponseBuilderTest extends TestCase
{
    private ResponseBuilder $responseBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responseBuilder = new ResponseBuilder();
    }

    public function testShouldReturnEmptyResponse(): void
    {
        $this->assertInstanceOf(Response::class, $this->responseBuilder->getResponse());
    }

    public function testShouldReturnResponseWithContent(): void
    {
        $content = '{"text": "i am a string"}';

        $response = $this->responseBuilder
            ->setContent($content)
            ->getResponse();

        $this->assertEquals($content, $response->getContent());
        $this->assertEquals(null, $response->headers->get('content-type'));
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testShouldResponseWithCustomStatusCode(): void
    {
        $response = $this->responseBuilder->setStatus(Response::HTTP_CREATED)->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testShouldResponseWithHeaders(): void
    {
        $response = $this->responseBuilder
            ->setContentType(ContentTypeHeader::fromString('application/json'))
            ->getResponse();

        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }

    /**
     * @dataProvider providerShouldResponseWithProblem
     */
    public function testShouldResponseWithProblem(int $status, string $expected): void
    {
        $response = $this->responseBuilder
            ->setContentType(ContentTypeHeader::fromString('application/json'))
            ->setStatus($status)
            ->getResponse();

        $this->assertEquals($expected, $response->headers->get('content-type'));
    }

    public function providerShouldResponseWithProblem(): array
    {
        return [
            [400, 'application/problem+json'],
            [403, 'application/problem+json'],
            [500, 'application/json'],
        ];
    }
}
