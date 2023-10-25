<?php

namespace Plank\LaravelHush\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Plank\LaravelHush\Tests\Models\Document;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->title,
            'body' => $this->faker->paragraphs(3, true),
        ];
    }
}
