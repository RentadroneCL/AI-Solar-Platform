<?php

namespace Database\Factories;

use App\Models\{Site, EquipmentType};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EquipmentType>
 */
class EquipmentTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EquipmentType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'site_id' => Site::factory()->create(),
            'name' => $this->faker->name,
            'quantity' => $this->faker->randomNumber(5, true),
            'custom_properties' => [],
        ];
    }
}
