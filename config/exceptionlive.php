<?php

use ExceptionLive\ExceptionLive;

return [
    'api_key' => env("EXCEPTION_LIVE_API_KEY"),

    'environment' => [
        'name' => env("APP_ENV"),
        'include' => [],
    ],

    'project_root' => base_path(),

    'hostname' => gethostname(),

    'excluded_exceptions' => [],

    'handlers' => [
        'exception' => true,
        'error' => true,
    ],

    'client' => [
        'timeout' => 0,
        'proxy' => [],
    ],
];
