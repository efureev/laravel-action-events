<?php

namespace Fureev\ActionEvents\Tests\Database\Factories;

use Fureev\ActionEvents\Tests\Entity\Models\Stuff;
use Illuminate\Database\Eloquent\Factories\Factory;

class StuffFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Stuff::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
