<?php

namespace Database\Factories;

use App\Models\Poli; // Import the Poli model
use Illuminate\Database\Eloquent\Factories\Factory;

class PoliFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Poli::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nama' => $this->faker->word(), // Generate a fake word for the name
            'status' => $this->faker->randomElement([0, 1]), // Generate a random status (0 or 1)
        ];
    }
}