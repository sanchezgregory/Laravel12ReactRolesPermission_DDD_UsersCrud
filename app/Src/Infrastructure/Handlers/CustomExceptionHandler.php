<?php

namespace App\Src\Infrastructure\Handlers;

use App\Src\Domain\Traits\LogExceptionTrait;
use App\Src\Infrastructure\Exceptions\ExceptionTransformer;
use App\Src\Infrastructure\Services\ExceptionService as ServicesExceptionService;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class CustomExceptionHandler implements ExceptionHandler
{
    use LogExceptionTrait;

    protected ExceptionHandler $originalHandler;

    public function __construct(ExceptionHandler $originalHandler)
    {
        $this->originalHandler = $originalHandler;
    }

    public function report(Throwable $e): void
    {
        // Basic log
        $this->logBasicInfo($e);

        // Detailed log
        $this->logDetailedInfo($e);
    }

    public function shouldReport(Throwable $e): bool
    {
        return $this->originalHandler->shouldReport($e);
    }

    public function render($request, Throwable $e): \Symfony\Component\HttpFoundation\Response
    {
        try {
            $transformedException = app(ExceptionTransformer::class)->transform($e);
            return app(ServicesExceptionService::class)->handle($transformedException, $request);
        } catch (\Throwable $handlerException) {
            // Just log the error and continue with the original handler
            Log::error("Custom exception handler failed", [
                'original_exception' => $e->getMessage(),
                'handler_exception' => $handlerException->getMessage()
            ]);
        }

        // Fallback to the original handler
        return $this->originalHandler->render($request, $e);
    }

    public function renderForConsole($output, Throwable $e)
    {
        return $this->originalHandler->renderForConsole($output, $e);
    }
}
