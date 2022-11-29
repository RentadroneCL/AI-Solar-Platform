<?php

namespace Database\Factories;

use Illuminate\Support\Carbon;
use App\Models\{User, Site, Inspection, Media};
use Illuminate\Database\Eloquent\Factories\Factory;

class InspectionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Inspection::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'site_id' => Site::factory(),
            'name' => $this->faker->name,
            'commissioning_date' => Carbon::now()->toDateTime(),
            'custom_properties' => [],
        ];
    }
}
