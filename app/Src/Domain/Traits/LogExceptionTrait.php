<?php

namespace App\Src\Domain\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Throwable;

trait LogExceptionTrait
{
    protected function getOrGenerateErrorCode(): string
    {
        if (Session::has('error_code')) {
            return Session::get('error_code');
        }

        $errorCode = strtoupper(uniqid(rand(1000, 9999), true));

        Session::put('error_code', $errorCode);

        return $errorCode;
    }
    protected function logBasicInfo(Throwable $e): void
    {
        try {
            $errorId = $this->getOrGenerateErrorCode();
            $basicInfo = sprintf(
                "[%s] %s in %s line %d - %s",
                $this->getErrorCode($e) . " - " . $errorId,
                get_class($e),
                basename($e->getFile()),
                $e->getLine(),
                $e->getMessage()
            );

            if ($e->getPrevious() !== null) {
                $previous = $e->getPrevious();
                $basicInfo .= sprintf(
                    " (Original: %s - %s)",
                    get_class($previous),
                    $previous->getMessage()
                );
            }

            Log::error($basicInfo);
        } catch (\Exception $logError) {
            Log::error("Error logging: " . $logError->getMessage());
        }
    }

    protected function logDetailedInfo(Throwable $e): void
    {
        try {
            $errorId = $this->getOrGenerateErrorCode();
            $errorCode = $this->getErrorCode($e);

            $exceptionToLog = (method_exists($e, 'getPrevious') && $e->getPrevious() !== null)
                ? $e->getPrevious()
                : $e;

            $errorDetails = sprintf(
                "Detailed Error Report\n" .
                    "Error Code: %s\n" .
                    "Type: %s\n" .
                    "Message: %s\n" .
                    "Location: %s:%d\n" .
                    "Stack Trace:\n%s",
                $errorCode,
                get_class($exceptionToLog),
                $exceptionToLog->getMessage(),
                $exceptionToLog->getFile(),
                $exceptionToLog->getLine(),
                $this->formatStackTrace($exceptionToLog->getTrace(), 10)
            );

            if ($e->getPrevious() !== null) {
                $previous = $e->getPrevious();
                $errorDetails .= sprintf(
                    "\n\nOriginal Exception:\n" .
                        "Type: %s\n" .
                        "Message: %s\n" .
                        "Location: %s:%d\n" .
                        "Stack Trace:\n%s",
                    get_class($previous),
                    $previous->getMessage(),
                    $previous->getFile(),
                    $previous->getLine(),
                    $this->formatStackTrace($previous->getTrace(), 10)
                );
            }

            if (app()->has('request')) {
                $request = app('request');
                $errorDetails .= "\n\nRequest Info:";
                $errorDetails .= "\nURL: " . $request->fullUrl();
                $errorDetails .= "\nMethod: " . $request->method();
                $errorDetails .= "\nIP: " . $request->ip();
                $errorDetails .= "\nUser-Agent: " . $request->header('User-Agent');

                if (auth()->check()) {
                    $errorDetails .= "\nUser ID: " . auth()->id();
                }
            }
            Log::channel('detailed_errors')->error("\n\n============== ErrorID: $errorId =============");
            Log::channel('detailed_errors')->error($errorDetails);
        } catch (\Exception $logError) {
            Log::error("Error logging detailed exception: " . $logError->getMessage());
        }
    }

    protected function formatStackTrace(array $trace, int $limit = 10): string
    {
        // Si el trace está vacío, es probable que sea un error de compilación/parsing
        if (empty($trace)) {
            return "[No stack trace available - This is likely a compilation/parsing error that occurred before code execution]";
        }

        $result = [];
        $count = min($limit, count($trace));

        for ($i = 0; $i < $count; $i++) {
            $frame = $trace[$i];
            
            $file = $frame['file'] ?? '[internal function]';
            $line = $frame['line'] ?? 0;
            $class = $frame['class'] ?? '';
            $type = $frame['type'] ?? '';
            $function = $frame['function'] ?? '';
            
            $result[] = sprintf(
                "#%d %s(%d): %s%s%s()",
                $i,
                $file,
                $line,
                $class,
                $type,
                $function
            );
        }

        return implode("\n", $result);
    }

    protected function getErrorCode(Throwable $e): string
    {
        if (method_exists($e, 'getErrorCode')) {
            return $e->getErrorCode();
        }

        if ($e instanceof \Illuminate\Database\QueryException) {
            return 'DB_ERROR';
        }

        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return 'VALIDATION_ERROR';
        }

        return 'SYSTEM_ERROR';
    }
}
