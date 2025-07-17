<?php

namespace App\Src\Infrastructure\Handlers;

use App\Src\Domain\Exceptions\UserFacingException;
use App\Src\Domain\Traits\LogExceptionTrait;

class DomainExceptionHandler
{
    use LogExceptionTrait;

    public function handle(\Throwable $exception): string
    {
        if (!($exception instanceof UserFacingException) || $exception->getPrevious() !== null) {
            // Essential information only.
            $this->logBasicInfo($exception);

            // Detailed information.
            $this->logDetailedInfo($exception);
        }

        return $this->getUserFriendlyMessage($exception);
    }

    private function getUserFriendlyMessage(\Throwable $e): string
    {
        // if app is in local mode, return the error message

        if (app()->environment('local', 'development')) {
            return $e->getMessage();
        }

        return 'Something went wrong';
    }
}
