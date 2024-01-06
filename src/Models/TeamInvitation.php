<?php

namespace IBroStudio\Teamable\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TeamInvitation extends Model
{
    use HasFactory;

    public function team(): HasOne
    {
        return $this->hasOne(Team::class);
    }

    public function invited(): HasOne
    {
        return $this->hasOne(
            related: config('auth.providers.users.model'),
            foreignKey: 'email',
            localKey: 'email'
        );
    }

    public function inviter(): HasOne
    {
        return $this->hasOne(config('auth.providers.users.model'));
    }
}
