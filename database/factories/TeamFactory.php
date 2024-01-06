<?php

namespace IBroStudio\Teamable\Database\Factories;

use IBroStudio\Teamable\Contracts\Teamable;
use IBroStudio\Teamable\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition()
    {
        return [
            'name' => fake()->company,
            'slug' => function (array $attributes) {
                return Str::slug($attributes['name']);
            },
        ];
    }

    public function teamable(Teamable $teamable): Factory
    {
        return $this->state(function (array $attributes) use ($teamable) {
            return [
                'teamable_id' => $teamable->id,
                'teamable_type' => get_class($teamable),
            ];
        });
    }
}
