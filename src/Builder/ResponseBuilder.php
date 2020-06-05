<?php

declare(strict_types=1);

namespace TerryApiBundle\Builder;

use Symfony\Component\HttpFoundation\Response;
use TerryApiBundle\ValueObject\AbstractHTTPClient;
use TerryApiBundle\ValueObject\HTTPClient;

class ResponseBuilder
{
    private string $content = '';

    private int $status = Response::HTTP_OK;

    private $client;

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

    private function generateHeaders(): array
    {
        /** @var array<string, string> $headers */
        $headers = [];

        if ($this->client instanceof HTTPClient) {
            $headers[AbstractHTTPClient::CONTENT_TYPE] = $this->client->negotiateContentType();
            $headers[AbstractHTTPClient::CONTENT_LANGUAGE] = $this->client->symfonyLocale();
        }

        return $headers;
    }
}
