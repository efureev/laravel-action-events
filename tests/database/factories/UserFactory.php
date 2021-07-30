<?php

namespace Fureev\ActionEvents\Tests\Database\Factories;

use Fureev\ActionEvents\Tests\Entity\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name'              => $this->faker->name,
            'last_name'         => $this->faker->lastName,
            'first_name'        => $this->faker->firstName,
            'email'             => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password'          => 'moscow',
        ];
    }
}
