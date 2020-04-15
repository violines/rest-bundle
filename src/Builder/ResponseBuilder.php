<?php

declare(strict_types=1);

namespace TerryApiBundle\Builder;

use Symfony\Component\HttpFoundation\Response;

class ResponseBuilder
{
    private string $content = '';
    private int $status = Response::HTTP_OK;
    private array $headers = [];

    public function getResponse(): Response
    {
        return new Response($this->content, $this->status, $this->headers);
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

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }
}
