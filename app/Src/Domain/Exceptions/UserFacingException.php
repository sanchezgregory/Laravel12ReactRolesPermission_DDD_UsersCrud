<?php

namespace App\Src\Domain\Exceptions;

class UserFacingException extends DomainException
{
    protected bool $isUserFacing = true;
}
