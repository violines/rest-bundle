<?php

declare(strict_types=1);

namespace TerryApiBundle\Builder;

use Symfony\Component\HttpFoundation\Response;

class ResponseBuilder
{
    private const CONTENT = 'content';
    private const STATUS = 'status';
    private const HEADERS = 'headers';

    private array $responseSet = [
        self::CONTENT => '',
        self::STATUS => Response::HTTP_OK,
        self::HEADERS => []
    ];

    public function getResponse(): Response
    {
        return new Response(...array_values($this->responseSet));
    }

    public function setContent(string $content): self
    {
        $this->responseSet[self::CONTENT] = $content;

        return $this;
    }

    public function setStatus(int $status): self
    {
        $this->responseSet[self::STATUS] = $status;

        return $this;
    }

    public function setHeaders(array $headers): self
    {
        $this->responseSet[self::HEADERS] = $headers;

        return $this;
    }
}
