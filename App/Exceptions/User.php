<?php

namespace App\Exceptions;

use Exception;

// This trait is used to handle user-specific exceptions
trait UserExceptions
{
    public function userAlreadyExists($message = 'User already exists', $code = 409, Exception $previous = null)
    {
        return new Exception($message, $code, $previous);
    }

    public function userNotFound($message = 'User not found', $code = 404, Exception $previous = null)
    {
        return new Exception($message, $code, $previous);
    }

    public function userNotCreated($message = 'User not created', $code = 400, Exception $previous = null)
    {
        return new Exception($message, $code, $previous);
    }

    public function userNotDeleted($message = 'User not deleted', $code = 400, Exception $previous = null)
    {
        return new Exception($message, $code, $previous);
    }
}

class User extends Exception
{
    use UserExceptions;
}
