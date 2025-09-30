<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\Config\RectorConfig;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameVariableToMatchNewTypeRector;

return RectorConfig::configure()
    // Define which paths Rector should look into
    // https://getrector.com/documentation/define-paths
    ->withPaths([
    ])
    // This will get the PHP version from composer and use its set
    // https://getrector.com/documentation/set-lists#content-php-sets
    ->withPhpSets()
    // Here we can define, what prepared sets of rules will be applied
    // https://getrector.com/documentation/set-lists
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        naming: true,
        instanceOf: true,
        earlyReturn: true,
        strictBooleans: true,
        carbon: true,
    )
    // Skip this rules from the sets
    ->withSkip([
        // Code style
        // https://getrector.com/find-rule?rectorSet=core-coding-style&activeRectorSetGroup=core
        CatchExceptionNameMatchingTypeRector::class,
        EncapsedStringsToSprintfRector::class,

        // Naming
        // https://getrector.com/find-rule?rectorSet=core-naming&activeRectorSetGroup=core
        RenamePropertyToMatchTypeRector::class,
        RenameParamToMatchTypeRector::class,
        RenameVariableToMatchNewTypeRector::class,
        RenameVariableToMatchMethodCallReturnTypeRector::class,
    ]);
