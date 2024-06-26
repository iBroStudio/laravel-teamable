<?php

namespace IBroStudio\Teamable\Concerns;

use IBroStudio\Teamable\Models\Team;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait IsTeamable
{
    public function initializeIsTeamable()
    {
        $this->with[] = 'team';
    }

    public static function bootIsTeamable(): void
    {
        static::created(function (Model $model) {
            if (config('teamable.auto.create')) {
                // @phpstan-ignore-next-line
                $model->createTeam();
            }
        });

        // @phpstan-ignore-next-line
        static::deleting(fn (Model $model) => $model->team->delete());
    }

    public function team(): MorphOne
    {
        return $this->morphOne(related: Team::class, name: 'teamable');
    }

    public function teamNameAttribute(): string
    {
        return config('teamable.model_name_attribute.'.self::class, config('teamable.model_name_attribute.default'));
    }

    public function createTeam(): self
    {
        $this->team()->create([
            // @phpstan-ignore-next-line
            'name' => $this->{$this->teamNameAttribute()},
        ]);

        return $this;
    }
}
