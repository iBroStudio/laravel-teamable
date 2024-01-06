<?php

namespace IBroStudio\Teamable\Tests\Support\Database\Factory;

use IBroStudio\Teamable\Tests\Support\Models\Teamable;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamableFactory extends Factory
{
    protected $model = Teamable::class;

    public function definition()
    {
        return [
            'name' => fake()->name(),
        ];
    }
}
