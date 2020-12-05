<?php

declare(strict_types=1);

namespace Violines\RestBundle\Error;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ValidationException extends \RuntimeException implements \Throwable
{
    private ConstraintViolationListInterface $violationList;

    private function __construct(ConstraintViolationListInterface $violationList)
    {
        $this->violationList = $violationList;
    }

    public static function fromViolationList(ConstraintViolationListInterface $violationList): self
    {
        return new self($violationList);
    }

    public function getViolationList(): ConstraintViolationListInterface
    {
        return $this->violationList;
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
