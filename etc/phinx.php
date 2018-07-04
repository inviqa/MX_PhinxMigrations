<?php

require __DIR__ . '/app/autoload.php';

$dbConfig = getPhinxDbConfig();

return [
    'paths' => [
        'migrations' => 'src/*/*/etc/migrations',
    ],
    'templates' => [
        'file' => 'src/*/Migrate/etc/Migration.php.txt',
    ],
    'environments' => [
        'default_database' => $dbConfig['dbname'],
        'toom' => [
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
