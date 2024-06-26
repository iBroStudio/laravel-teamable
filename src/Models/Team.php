<?php

namespace IBroStudio\Teamable\Models;

use IBroStudio\Teamable\Database\Factories\TeamFactory;
use IBroStudio\Teamable\ValueObjects\TeamType;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Team extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * @return Attribute<TeamType, never>
     */
    protected function type(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => TeamType::make($attributes['teamable_type'])
        );
    }

    public function getType(): TeamType
    {
        return $this->type;
    }

    public function teamable(): MorphTo
    {
        return $this->morphTo();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            related: config('auth.providers.users.model'),
            table: 'team_user',
            foreignPivotKey: 'team_id',
            relatedPivotKey: 'user_id'
        )
            ->withTimestamps();
    }

    /**
     * @mixed Model
     */
    public function hasMember(Authenticatable $user): bool
    {
        return (bool) $this->users()->where('users.id', $user->getAuthIdentifier())->first();
    }

    public function invites(): HasMany
    {
        return $this->hasMany(related: TeamInvitation::class, foreignKey: 'team_id');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function newFactory(): Factory
    {
        return TeamFactory::new();
    }
}
