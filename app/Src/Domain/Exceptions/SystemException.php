<?php

namespace App\Src\Domain\Exceptions;

class SystemException extends DomainException
{
    protected bool $isUserFacing = false;
}
