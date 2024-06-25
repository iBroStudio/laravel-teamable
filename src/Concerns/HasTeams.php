<?php

namespace IBroStudio\Teamable\Concerns;

use IBroStudio\Teamable\Models\Team;
use IBroStudio\Teamable\ValueObjects\CurrentTeam;
use IBroStudio\Teamable\ValueObjects\TeamType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Gate;

trait HasTeams
{
    public function teams(?TeamType $type = null): BelongsToMany
    {
        return $this->belongsToMany(
            related: Team::class,
            table: 'team_user',
            foreignPivotKey: 'user_id',
            relatedPivotKey: 'team_id'
        )
            ->when($type, function (Builder $query, TeamType $type) {
                $query->where('teamable_type', $type->class);
            })
            ->withTimestamps();
    }

    public function currentTeamId(TeamType $teamType, ?Team $team = null): ?int
    {
        if ($team) {
            $data = CurrentTeam::make(
                id: $team->id,
                teamType: $team->type
            );

            $this->data_repository()->add(
                data: $data,
                valuesAttributes: [
                    'values->teamType' => $team->type->class,
                ]
            );

            return $team->id;
        }
        //dd($this);
        try {
            return $this->data_repository(
                dataClass: CurrentTeam::class,
                valuesQuery: ['teamType' => $teamType->class]
            )
                ->values()
                ->id();
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    public function clearCurrentTeamId(TeamType $teamType): bool
    {
        return $this->data_repository(
            dataClass: CurrentTeam::class,
            valuesQuery: ['teamType' => $teamType->class]
        )
            ->delete();
    }

    public function attachTeam(Team $team, array $pivotData = []): self
    {
        /*
        if (! $this->relationLoaded($team->type->relation())) {
            $this->load($team->type->relation());
        }
        */
        if (! $this->relationLoaded('teams')) {
            $this->load('teams');
        }

        if (! $this->teams()
            ->get()
            ->contains($team)
        ) {
            $this->teams()->attach($team, $pivotData);

            //event(new UserJoinedTeam($this, $team));

            /*
            if ($this->relationLoaded($team->type->relation())) {
                $this->load($team->type->relation());
            }
            */

            $this->load('teams');

            if (is_null($this->currentTeamId($team->type))) {
                $this->switchTeam($team);
            }
        }

        return $this;
    }

    public function detachTeam(Team $team): self
    {
        $this->teams()->detach($team);

        //event(new UserLeftTeam($this, $team));
        /*
                if ($this->relationLoaded($team->type->relation())) {
                    $this->load($team->type->relation());
                }
        */
        $this->load('teams');

        if ($this->teams($team->type)->count() === 0
            || $this->currentTeamId($team->type) === $team->id
        ) {
            $this->clearCurrentTeamId($team->type);

            $this->load('teams');
        }

        return $this;
    }

    public function switchTeam(Team|Model|int $team): self
    {
        if (is_int($team)) {
            $team = Team::findOrFail($team);
        }

        Gate::authorize('switch-team', [$team, $this]);

        $this->currentTeamId($team->type, $team);

        if ($this->relationLoaded('teams')) {
            $this->load('teams');
        }

        return $this;
    }
}
