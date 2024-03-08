<?php

namespace App\Exceptions;

class FirewallException extends TemplateException
{
    public function ipAlreadyExists() : self
    {
        return new self('IP already exists', 400);
    }
    public function ipDoesNotExist() : self
    {
        return new self('IP does not exist', 400);
    }
    public function ipNotUpdated() : self
    {
        return new self('IP not updated', 500);
    }
    public function invalidIP() : self
    {
        return new self('invalid IP', 400);
    }
}
