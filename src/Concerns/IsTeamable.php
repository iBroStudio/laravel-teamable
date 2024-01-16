<?php

namespace IBroStudio\Teamable\Concerns;

use IBroStudio\Teamable\Models\Team;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait IsTeamable
{
    public static function bootIsTeamable(): void
    {
        static::created(fn (Model $model) => $model->team()->create([
            'name' => $model->{$model->teamNameAttribute()},
        ])
        );

        static::deleting(fn (Model $model) => $model->team->delete()
        );
    }

    public function team(): MorphOne
    {
        return $this->morphOne(related: Team::class, name: 'teamable');
    }

    public function teamNameAttribute(): string
    {
        return config('teamable.model_name_attribute.'.self::class, config('teamable.model_name_attribute.default'));
    }
}
