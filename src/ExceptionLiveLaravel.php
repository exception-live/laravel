<?php

namespace ExceptionLive\ExceptionLiveLaravel;

use ExceptionLive\ExceptionLive;

class ExceptionLiveLaravel
{
    /**
     * Package Version
     */
    const VERSION = '0.1';

    /**
     * @param array $config
     * @return ExceptionLive
     */
    public function make($config): ExceptionLive
    {
        return new ExceptionLive(array_merge([
            'notifier' => [
                'name' => 'Exception.Live Laravel',
                'url' => 'https://github.com/exception-live/laravel',
                'version' => self::VERSION,
            ],
        ], $config));
    }
}
