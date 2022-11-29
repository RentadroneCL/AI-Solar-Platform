<?php

namespace Database\Factories;

use App\Models\{Equipment, EquipmentType};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Equipment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'equipment_type_id' => EquipmentType::factory()->create(),
            'uuid' => $this->faker->uuid(),
            'name' => $this->faker->name,
            'brand' => $this->faker->company(),
            'model' => $this->faker->name,
            'serial' => $this->faker->randomNumber(5, true),
            'custom_properties' => [],
        ];
    }
}
