<?php

namespace App\Src\Domain\Exceptions;

use App\Src\Domain\Traits\LogExceptionTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

abstract class DomainException extends Exception
{
    use LogExceptionTrait;
    protected string $errorCode;
    protected bool $isUserFacing = false; // By default, all exceptions are not considered user facing

    public function __construct(string $message = "", int $code = 0, \Throwable $previous)
    {
        $this->errorCode = $this->getOrGenerateErrorCode();
        parent::__construct($message, $code, $previous);
        $this->logError();
    }
    public function isUserFacing(): bool
    {
        return $this->isUserFacing;
    }
    protected function logError(): void
    {
        $userId = null;
        $id = 'UserId';
        if (Auth::guard('web')->check()) {
            $userId = Auth::guard('web')->id();
        }

        $logMessage = sprintf(
            "[Error code: %s] [%s %d] %s in %s on line %d",
            $this->getErrorCode(),
            $id,
            $userId,
            $this->getMessage(),
            $this->getFile(),
            $this->getLine()
        );

        Log::error($logMessage);
    }
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}
