<?php

namespace IBroStudio\Teamable\Tests\Support\Models;

use IBroStudio\Teamable\Concerns\IsTeamable;
use IBroStudio\Teamable\Tests\Support\Database\Factory\OtherTeamableFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherTeamable extends Model implements \IBroStudio\Teamable\Contracts\Teamable
{
    use HasFactory;
    use IsTeamable;

    protected static function newFactory()
    {
        return OtherTeamableFactory::new();
    }
}
