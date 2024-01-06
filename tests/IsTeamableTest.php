<?php

use IBroStudio\Teamable\Models\Team;
use IBroStudio\Teamable\Tests\Support\Models\Teamable;

test('teamable has team', function () {
    $teamable = Teamable::factory()->create();
    $team = Team::factory()->teamable($teamable)->create();

    expect($teamable->team->is($team))->toBeTrue();
});
