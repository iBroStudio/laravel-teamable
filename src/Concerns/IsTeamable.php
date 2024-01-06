<?php

namespace IBroStudio\Teamable\Concerns;

use IBroStudio\Teamable\Models\Team;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait IsTeamable
{
    public function team(): MorphOne
    {
        return $this->morphOne(related: Team::class, name: 'teamable');
    }
}
