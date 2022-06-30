<?php
return [
    'DB_DSN' => 'sqlite:'.__DIR__.'/testdb.sqlite',
    'DB_USER' => '',
    'DB_PASSWORD' => '',

    'TWIG_CACHE' => false,

    'LOG_FILE' => '/dev/stdout',
    'LOG_JSON' => true,

    'DI_CONSTRUCTOR_ARGUMENTS' => [
        'Readdle\QuickBike\Test\DITestClasses\DITestClass' => [
            'someToken' => 'abc',
            'otherValue' => 42
        ]
    ],

    // this is a hash of 'test' for testing purposes
    // it has been split to substrings, so GitHub crawlers won't think it is something important
    'USERS' => [ 'admin' => ['pwd' => '$'.'2y'.'$10$2vdO8TgQxR8zCM6JvDLl0uuZI//9j48P8MfYz5AzDqlEVn96vkCgW' ]],
];
