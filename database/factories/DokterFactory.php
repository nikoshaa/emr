<?php

namespace Database\Factories;

use App\Models\Dokter; // Import the Dokter model
use App\Models\Poli;   // Import the Poli model for relation
use App\Models\User;   // Import the User model for relation
use Illuminate\Database\Eloquent\Factories\Factory;

class DokterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Dokter::class; // Set the model class

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nip' => $this->faker->unique()->numerify('##########'), // Generate a unique 10-digit NIP
            'nama' => $this->faker->name(), // Generate a fake name
            'no_hp' => $this->faker->numerify('###########'), // Generate an 11-digit phone number
            'alamat' => $this->faker->address(), // Generate a fake address
            'poli' => Poli::factory()->create()->nama, // Associate with a Poli (creates one if none exist)
            'status' => $this->faker->randomElement([0, 1]), // Generate a random status (0 or 1)
            'user_id' => User::factory()->state(['role' => 3]),  // Associate with a User (creates one if none exist)
        ];
    }
}
