<?php

declare(strict_types=1);

namespace TerryApiBundle\Response;

use Symfony\Component\HttpFoundation\Response;
use TerryApiBundle\HttpClient\HttpClient;

final class ResponseBuilder
{
    private const PROBLEM = 'problem+';
    private string $content = '';
    private int $status = Response::HTTP_OK;
    private ?HttpClient $client = null;

    public function getResponse(): Response
    {
        return new Response($this->content, $this->status, $this->generateHeaders());
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function setClient(HTTPClient $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    private function generateHeaders(): array
    {
        /** @var array<string, string> $headers */
        $headers = [];

        if ($this->client instanceof HttpClient) {
            $contentType = $this->client->negotiateContentType();
            if (400 <= $this->status && 500 > $this->status) {
                $contentType = $this->withProblem($contentType);
            }
            $headers[HttpClient::CONTENT_TYPE] = $contentType;
        }

        return $headers;
    }

    private function withProblem(string $contentType): string
    {
        $problemContentType = '';

        $parts = explode('/', $contentType);

        $limit = count($parts) - 1;

        for ($i = $limit; $i >= 0; $i--) {
            if ($i !== $limit) {
                $problemContentType = '/' . $problemContentType;
            }

            $problemContentType = $parts[$i] . $problemContentType;

            if ($i === $limit) {
                $problemContentType = self::PROBLEM . $problemContentType;
            }
        }

        return $problemContentType;
    }
}
