<?php

use IBroStudio\Teamable\Models\Team;
use IBroStudio\Teamable\Tests\Support\Models\Teamable;
use IBroStudio\Teamable\Tests\Support\Models\User;
use IBroStudio\Teamable\ValueObjects\TeamType;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertModelExists;

it('can create team', function () {
    $team = Team::factory()
        ->teamable(
            Teamable::factory()->create()
        )
        ->create();

    assertModelExists($team);

    assertDatabaseHas(Team::class, [
        'name' => $team->name,
        'slug' => $team->slug,
        'teamable_id' => $team->teamable_id,
        'teamable_type' => $team->teamable_type,
    ]);
});

it('has a type attribute', function () {
    $team = Team::factory()
        ->teamable(
            Teamable::factory()->create()
        )
        ->create();

    expect($team->type)->toBeInstanceOf(TeamType::class);
    expect($team->type->class)->toBe(Teamable::class);
    expect($team->type->value())->toBe('teamable');
    expect($team->type->relation())->toBe('teamables');
    expect($team->type->currentRelation())->toBe('currentTeamable');
    expect($team->type->currentId())->toBe('current_teamable_id');
});

it('can generate a slug', function () {
    $team = Team::factory()
        ->teamable(
            Teamable::factory()->create()
        )
        ->create();

    assertDatabaseHas(Team::class, [
        'slug' => Str::slug($team->name),
    ]);
});

it('has a teamable relation', function () {
    $teamable = Teamable::factory()->create();
    $team = Team::factory()->teamable($teamable)->create();

    expect($team->teamable->is($teamable))->toBeTrue();
});

it('has a users relation', function () {
    $user = User::factory()->create();
    $team = Team::factory()
        ->teamable(
            Teamable::factory()->create()
        )
        ->hasAttached($user)
        ->create();

    expect($team->users()->first()->is($user))->toBeTrue();
});

it('can check user membership', function () {
    $user = User::factory()->create();
    $non_member = User::factory()->create();
    $team = Team::factory()
        ->teamable(
            Teamable::factory()->create()
        )
        ->hasAttached($user)
        ->create();

    expect($team->hasMember($user))->toBeTrue();

    expect($team->hasMember($non_member))->toBeFalse();
});
