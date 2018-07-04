<?php

require __DIR__ . '/app/autoload.php';

$dbConfig = getPhinxDbConfig();

return [
    'paths' => [
        'migrations' => __DIR__. '/src/*/*/etc/migrations',
    ],
    'templates' => [
        'file' => __DIR__ . '/vendor/mx/module-phinx-migrations/etc/Migration.php.txt',
    ],
    'environments' => [
        'default_database' => $dbConfig['dbname'],
        $dbConfig['dbname'] => [
            'adapter' => 'mysql',
            'name' => $dbConfig['dbname'],
            'connection' => new PDO(
                sprintf('mysql:host=%s;dbname=%s', $dbConfig['host'], $dbConfig['dbname']),
                $dbConfig['username'],
                $dbConfig['password'],
                $dbConfig['driver_options']
            ),
        ]
    ]
];
