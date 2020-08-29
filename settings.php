<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Database con Elocuent (Illuminate)
        'db' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'u922010286_tebu',
            'username'  => 'u922010286_tebu',
            'password'  => 'tridim05&Rama',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'   => '',
        ],
    ],
];
