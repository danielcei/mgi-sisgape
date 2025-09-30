<?php

declare(strict_types=1);

use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use SafeDeploy\Quality\PHPStan\Rules\NoNestedArrayRule;

uses()->group('PHPStanRules', 'NoNestedArrayRule');

it('getNodeType should return ArrayDimFetch', function (): void {
    $rule = new NoNestedArrayRule;

    $this->assertEquals(ArrayDimFetch::class, $rule->getNodeType());
});

it('it should detect nested arrays', function (): void {
    $rule = new NoNestedArrayRule;

    $nestedArray = new ArrayDimFetch(
        new ArrayDimFetch(
            new Variable('array'),
            new String_('key1')
        ),
        new String_('key2')
    );

    $scope = $this->createMock(Scope::class);

    $errors = $rule->processNode($nestedArray, $scope);

    expect($errors)->toBeArray();
    expect($errors)->toHaveCount(1);
    expect($errors[0])->toBeInstanceOf(RuleError::class);
    expect($errors[0]->getMessage())->toBe('Nested arrays are not allowed. Use Arr::get($array, "key1.key2") instead of $array[\'key1\'][\'key2\'].');
});

it('should not throw exception in non-nested arrays', function (): void {
    $rule = new NoNestedArrayRule;

    $simpleArray = new ArrayDimFetch(
        new Variable('array'),
        new String_('key')
    );

    $scope = $this->createMock(Scope::class);

    $errors = $rule->processNode($simpleArray, $scope);

    expect($errors)->toBeArray();
    expect($errors)->toHaveCount(0);
});
