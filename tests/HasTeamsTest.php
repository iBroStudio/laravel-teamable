<?php

use IBroStudio\Teamable\Models\Team;
use IBroStudio\Teamable\Tests\Support\Models\OtherTeamable;
use IBroStudio\Teamable\Tests\Support\Models\Teamable;
use IBroStudio\Teamable\Tests\Support\Models\User;
use IBroStudio\Teamable\ValueObjects\TeamType;
use Illuminate\Auth\Access\AuthorizationException;

use function Pest\Laravel\actingAs;

test('user can have team', function () {
    $team = Team::factory()
        ->teamable(
            Teamable::factory()->create()
        )
        ->create();
    $user = User::factory()
        ->hasAttached($team)
        ->create();

    expect($user->teams()->first()->is($team))->toBeTrue();
});

it('can filter teams by type', function () {
    $team1 = Team::factory()
        ->teamable(
            Teamable::factory()->create()
        )
        ->create();
    $team2 = Team::factory()
        ->teamable(
            OtherTeamable::factory()->create()
        )
        ->create();
    $user = User::factory()
        ->hasAttached($team1)
        ->hasAttached($team2)
        ->create();

    expect($user->teams)->toHaveCount(2);

    $filtered = $user->teams(TeamType::make(OtherTeamable::class));

    expect($filtered->get())->toHaveCount(1)
        ->and($filtered->first()->is($team2))->toBeTrue();

});

it('can attach team', function () {
    actingAs(User::factory()->create());
    $team = Team::factory()
        ->teamable(
            Teamable::factory()->create()
        )
        ->create();
    $user = User::factory()->create();
    $user->attachTeam($team);

    expect($user->teams()->first()->is($team))->toBeTrue();
    expect($user->currentTeamId($team->type))->toBe($team->id);
});

it('can detach team', function () {
    actingAs(User::factory()->create());
    $team = Team::factory()
        ->teamable(
            Teamable::factory()->create()
        )
        ->create();
    $user = User::factory()->create();
    $user->attachTeam($team);
    $user->detachTeam($team);

    expect($user->teams)->toHaveCount(0);
    expect($user->currentTeamId($team->type))->toBeNull();
});

it('can set and retrieve current team ID', function () {
    $team = Team::factory()
        ->teamable(
            Teamable::factory()->create()
        )
        ->create();
    $user = User::factory()->create();
    $user->currentTeamId($team->type, $team);

    expect($user->currentTeamId($team->type))->toBe($team->id);
});

it('can clear current team ID', function () {
    $team = Team::factory()
        ->teamable(
            Teamable::factory()->create()
        )
        ->create();
    $user = User::factory()->create();
    $user->currentTeamId($team->type, $team);
    $user->clearCurrentTeamId($team->type);

    expect($user->currentTeamId($team->type))->toBeNull();
});

it('can switch team', function () {
    actingAs(User::factory()->create());
    $team = Team::factory()
        ->teamable(
            Teamable::factory()->create()
        )
        ->create();
    $team2 = Team::factory()
        ->teamable(
            Teamable::factory()->create()
        )
        ->create();
    $user = User::factory()->create();
    $user->attachTeam($team);
    $user->attachTeam($team2);

    expect($user->currentTeamId($team->type))->toBe($team->id);

    $user->switchTeam($team2);

    expect($user->currentTeamId($team->type))->toBe($team2->id);
});

it('disallows non member to switch to a team', function () {
    actingAs(User::factory()->create());
    $team = Team::factory()
        ->teamable(
            Teamable::factory()->create()
        )
        ->create();
    $user = User::factory()->create();
    $user->switchTeam($team);
})->throws(AuthorizationException::class);

it('can set multiple current ID for different team', function () {
    actingAs(User::factory()->create());
    $team = Team::factory()
        ->teamable(
            Teamable::factory()->create()
        )
        ->create();
    $team2 = Team::factory()
        ->teamable(
            OtherTeamable::factory()->create()
        )
        ->create();
    $user = User::factory()->create();
    $user->attachTeam($team);
    $user->attachTeam($team2);

    expect($user->currentTeamId($team->type))->toBe($team->id);
    expect($user->currentTeamId($team2->type))->toBe($team2->id);
});
/*
it('can set and retrieve current team ID', function () {
    $team = Team::factory()
        ->teamable(
            Teamable::factory()->create()
        )
        ->create();
    $user = User::factory()->create();
    $user->currentTeamId($team->type, $team);

    expect($user->currentTeamId($team->type))->toBe($team->id);
});
 */
