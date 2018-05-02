<?php

use Composer\Factory as ComposerFactory;

function getPhinxDbConfig(): array
{
    $root = dirname(ComposerFactory::getComposerFile());

    $env = require $root .'/app/etc/env.php';

    $dbConfig = $env['db']['connection']['default'];

    if (!isset($dbConfig['driver_options'])) {
        $dbConfig['driver_options'] = [];
    }

    return $dbConfig;
}
