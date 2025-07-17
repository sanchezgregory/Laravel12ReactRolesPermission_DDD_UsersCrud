<?php

namespace App\Src\Infrastructure\Exceptions;

use App\Src\Domain\Exceptions\SystemException;

class RepositoryException extends SystemException
{
    public function __construct(string $message = "Repository error", int $code = 0, \Throwable $previous)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getErrorCode(): string
    {
        return 'REPOSITORY_ERROR';
    }
}
