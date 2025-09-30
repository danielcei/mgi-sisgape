<?php

declare(strict_types=1);

use Rector\Configuration\RectorConfigBuilder;

/**
 * We will use the same config as the projects using SafeDeploy.
 * For that, then, we load the config file from dist folder.
 *
 * @var RectorConfigBuilder $rectorConfig
 */
$rectorConfig = require __DIR__.'/dist/rector.php';

return $rectorConfig
    // Define which paths Rector should look into
    // https://getrector.com/documentation/define-paths
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/tests',
        __DIR__.'/workbench',
    ]);
