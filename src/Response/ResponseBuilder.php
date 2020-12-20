<?php

declare(strict_types=1);

namespace Violines\RestBundle\Response;

use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
final class ResponseBuilder
{
    private string $content = '';
    private int $status = Response::HTTP_OK;
    private ?ContentTypeHeader $contentType = null;

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

    public function setContentType(ContentTypeHeader $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    private function generateHeaders(): array
    {
        /** @var array<string, string> $headers */
        $headers = [];

        if (null !== $this->contentType) {
            $headers[ContentTypeHeader::NAME] = 400 <= $this->status && 500 > $this->status
                ? $this->contentType->toStringWithProblem()
                : $this->contentType->toString();
        }

        return $headers;
    }
}
