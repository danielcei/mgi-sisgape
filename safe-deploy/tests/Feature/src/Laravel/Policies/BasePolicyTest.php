<?php

declare(strict_types=1);

use Illuminate\Foundation\Auth\User;
use Mockery as m;
use SafeDeploy\Laravel\Policies\BasePolicy;

uses()->group('Policies', 'BasePolicy');

it('should return expected result for user permission', function (bool $expectedReturn): void {
    $mock = m::mock(User::class);

    $policy = new class extends BasePolicy {};

    $modelName = Str::kebab(class_basename($policy::class));
    $mock->shouldReceive('can')->with("view-{$modelName}")->andReturn($expectedReturn);

    $result = $policy->view($mock);

    $this->assertEquals($result, $expectedReturn);
})->with([
    'user has permission' => true,
    'user does not have permission' => false,
]);
