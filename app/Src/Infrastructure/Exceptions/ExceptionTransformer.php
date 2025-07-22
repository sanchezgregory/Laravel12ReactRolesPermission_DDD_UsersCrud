<?php


namespace App\Src\Infrastructure\Exceptions;

use App\Src\Domain\Exceptions\DomainException;
use App\Src\Domain\Exceptions\SystemException;
use Dotenv\Exception\InvalidFileException;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Filesystem\LockTimeoutException;
use Illuminate\Database\DeadlockException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\MaxAttemptsExceededException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use JsonException;
use LogicException;
use OutOfBoundsException;
use PDOException;
use RangeException;
use RuntimeException;
use SebastianBergmann\Invoker\TimeoutException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\LengthRequiredHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionRequiredHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Mailer\Exception\TransportException;
use UnderflowException;

class ExceptionTransformer
{
    /**
     * @var array Exceptions to user facing
     */
    private static array $SystemExceptions = [
        // Laravel Database/Eloquent
        ModelNotFoundException::class => 'Requested resource was not found',
        QueryException::class => 'Database operation failed',
        UniqueConstraintViolationException::class => 'This record already exists',

        // Laravel HTTP/Routing
        NotFoundHttpException::class => 'Resource not found',
        MethodNotAllowedHttpException::class => 'Operation not allowed',
        TooManyRequestsHttpException::class => 'Too many attempts, please try again later',
        AccessDeniedHttpException::class => 'Access denied',

        // Laravel Auth
        AuthenticationException::class => 'Authentication required',
        AuthorizationException::class => 'You are not authorized to perform this action',
        TokenMismatchException::class => 'Session expired, please try again',

        // Laravel Validation
        // ValidationException::class => 'Validation failed, Invalid data provided',

        // Laravel Files/Storage
        FileNotFoundException::class => 'Required file not found',
        InvalidFileException::class => 'Invalid file format',

        // Laravel Cache
        // InvalidArgumentException::class => 'Invalid operation requested',
        LockTimeoutException::class => 'Operation timed out, please try again',

        // Laravel Queue
        TimeoutException::class => 'Operation took too long, please try again',
        MaxAttemptsExceededException::class => 'Process failed, please try again later',


        // PHP Built-in
        RuntimeException::class => 'Operation could not be completed',
        // LogicException::class => 'Invalid operation requested',
        OutOfBoundsException::class => 'Invalid data access attempt',
        UnderflowException::class => 'Operation cannot be completed',
        RangeException::class => 'Value out of acceptable range',

        // JSON
        JsonException::class => 'Invalid data format',

        // General HTTP
        ClientException::class => 'Request failed',
        ServerException::class => 'Server error occurred',
        BadResponseException::class => 'Invalid response received',
        ConnectException::class => 'Connection failed',

        // Specific HTTP Status
        BadRequestHttpException::class => 'Invalid request',
        ConflictHttpException::class => 'Operation conflicts with existing data',
        GoneHttpException::class => 'Resource no longer available',
        LengthRequiredHttpException::class => 'Invalid request length',
        PreconditionFailedHttpException::class => 'Precondition failed',
        PreconditionRequiredHttpException::class => 'Precondition required',
        ServiceUnavailableHttpException::class => 'Service temporarily unavailable',
        UnauthorizedHttpException::class => 'Authentication required',
        UnprocessableEntityHttpException::class => 'Unable to process request',
    ];

    public static function transform(\Throwable $e): \Throwable
    {
        // Only transform domain exceptions
        if ($e instanceof DomainException) {
            return $e;
        }

        // search for user system exceptions
        foreach (self::$SystemExceptions as $exceptionClass => $message) {
            if ($e instanceof $exceptionClass) {
                return new SystemException(
                    $message ?? $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
        }

        // For no specific error message, just return the original exception
        return $e;
    }
}
