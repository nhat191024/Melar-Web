<?php

namespace Database\Factories;

use App\Models\FormVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormVersion>
 */
class FormVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_id' => \App\Models\Form::factory(),
            'version_number' => fake()->numberBetween(1, 10),
            'schema' => [
                [
                    'id' => fake()->uuid(),
                    'type' => 'text',
                    'label' => 'Full Name',
                    'key' => 'full_name',
                    'required' => true,
                    'width' => 'full',
                    'placeholder' => 'Enter your name',
                    'help_text' => '',
                    'options' => [],
                    'validation' => ['min_length' => null, 'max_length' => 255],
                    'content' => null,
                ],
            ],
            'published_at' => now(),
        ];
    }
}
