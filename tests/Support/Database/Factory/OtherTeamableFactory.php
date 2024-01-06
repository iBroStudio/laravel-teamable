<?php

namespace IBroStudio\Teamable\Tests\Support\Database\Factory;

use IBroStudio\Teamable\Tests\Support\Models\OtherTeamable;
use Illuminate\Database\Eloquent\Factories\Factory;

class OtherTeamableFactory extends Factory
{
    protected $model = OtherTeamable::class;

    public function definition()
    {
        return [
            'name' => fake()->name(),
        ];
    }
}
