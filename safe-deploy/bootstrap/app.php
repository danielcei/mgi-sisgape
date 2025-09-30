<?php

declare(strict_types=1);

use Orchestra\Testbench\Concerns\CreatesApplication;
use Orchestra\Testbench\Foundation\Application;
use SafeDeploy\SafeDeployServiceProvider;

$basePathLocator = new class
{
    use CreatesApplication;
};

$app = new Application($basePathLocator::applicationBasePath())
    ->configure([
        'enables_package_discoveries' => true,
    ])
    ->createApplication();

$app->register(SafeDeployServiceProvider::class);

return $app;
