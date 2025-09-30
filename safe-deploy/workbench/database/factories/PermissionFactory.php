<?php

declare(strict_types=1);

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Workbench\App\Models\Permission;

/**
 * @template TModel of Permission
 *
 * @extends Factory<TModel>
 */
class PermissionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     *
     * @return array<model-property<Permission>, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'guard_name' => Arr::random(['web', 'api']),
        ];
    }
}
