<?php

declare(strict_types=1);

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Workbench\App\Models\User;

/**
 * @template TModel of User
 *
 * @extends Factory<TModel>
 */
class UserFactory extends Factory
{
    protected const int TOKEN_SIZE = 10;

    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<model-property<User>, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now()->toDateTimeString(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(self::TOKEN_SIZE),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(static fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }
}
