<?php

namespace ExceptionLive\ExceptionLiveLaravel\Exceptions;

class TestException extends \Exception
{
    /**
     * TestException constructor.
     */
    public function __construct()
    {
        parent::__construct("This is an example exception for ExceptionLive");
    }
}
