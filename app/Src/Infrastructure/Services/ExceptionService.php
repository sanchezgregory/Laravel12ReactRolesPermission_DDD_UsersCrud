<?php

namespace App\Src\Infrastructure\Services;

use App\Src\Domain\Exceptions\SystemException;
use App\Src\Domain\Exceptions\UserFacingException;
use App\Src\Infrastructure\Handlers\DomainExceptionHandler;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExceptionService
{
    protected DomainExceptionHandler $domainExceptionHandler;

    public function __construct(DomainExceptionHandler $domainExceptionHandler)
    {
        $this->domainExceptionHandler = $domainExceptionHandler;
    }

    public function handle(\Throwable $exception, Request $request)
    {
        // 2. Get user-friendly message and status code
        $message = $this->domainExceptionHandler->handle($exception);

        // 3. Determine http status code
        $statusCode = $this->getStatusCode($exception);

        // 4. Return response based on request type
        if ($request->expectsJson()) {
            return response()->json([
                'error' => [
                    'codigo' => $statusCode,
                    'mensaje' => $message,
                ]
            ]);
        }

        if (app()->bound('inertia') && $request->header('X-Inertia')) {
            return Inertia::render('Error', [
                'error' => $message,
                'status' => $statusCode
            ]);
        }

        return redirect()->back()->with('error', $message);
    }

    protected function getStatusCode(\Throwable $exception): int
    {
        if ($exception instanceof UserFacingException) {
            return 400; // Bad Request
        }

        if ($exception instanceof SystemException) {
            return 550; // Custom status code for system exceptions
        }

        if (
            $exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ||
            (method_exists($exception, 'getStatusCode') && $exception->getStatusCode() == 404)
        ) {
            return 404;
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return 401;
        }

        if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return 403;
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return 422;
        }

        return 500;
    }
}
