<?php

declare(strict_types=1);

use Workbench\App\Models\User;

uses()->group('Observers', 'UserstampsObserver');

it('sets user stamps', function (callable $operation, string $attribute): void {
    $targetModel = User::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user);

    $targetModel = $operation($targetModel);

    $this->assertSame($user->id, $targetModel->{$attribute});
})
    ->with([
        'creating user sets created_by' => [
            fn () => User::create(['name' => fake()->name, 'email' => fake()->email, 'password' => fake()->password]),
            'created_by',
        ],
        'creating user sets updated_by' => [
            fn () => User::create(['name' => fake()->name, 'email' => fake()->email, 'password' => fake()->password]),
            'updated_by',
        ],
        'updating user sets updated_by' => [
            fn ($targetModel) => tap($targetModel)->update(['name' => fake()->name]),
            'updated_by',
        ],
        'deleting user sets deleted_by' => [
            fn ($targetModel) => tap($targetModel)->delete(),
            'deleted_by',
        ],
    ]);

it('sets deleted_by to null when restoring', function (): void {
    $targetModel = User::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user);

    $targetModel->delete();

    $this->assertSame($user->id, $targetModel->deleted_by);

    $targetModel->restore();

    $this->assertNull($targetModel->deleted_by);
});
