<?php

use IBroStudio\Teamable\Tests\Support\Models\Teamable;
use Illuminate\Support\Facades\Config;

it('can have a team', function () {
    $teamable = Teamable::factory()->create();

    expect($teamable->team)->not->toBeNull()
        ->and($teamable->team->name)->toBe($teamable->{$teamable->teamNameAttribute()});
});

it('can provide teamNameAttribute', function () {
    $teamable = Teamable::factory()->create();

    expect($teamable->teamNameAttribute())->toBe('name');

    Config::set('teamable.model_name_attribute.'.Teamable::class, 'other_attribute');

    expect($teamable->teamNameAttribute())->toBe('other_attribute');
});

it('can delete team with teamable', function () {
    $teamable = Teamable::factory()->create();
    $teamable->delete();

    $this->assertDatabaseEmpty('teams');
});
