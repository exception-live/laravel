<?php

namespace ExceptionLive\ExceptionLiveLaravel\Facades;

use Illuminate\Support\Facades\Facade;

class ExceptionLive extends Facade
{
    /**
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'exceptionlive';
    }
}
