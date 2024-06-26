<?php

namespace IBroStudio\Teamable\Concerns;

use IBroStudio\DataRepository\Concerns\HasDataRepository;
use IBroStudio\Teamable\Models\Team;
use IBroStudio\Teamable\ValueObjects\CurrentTeam;
use IBroStudio\Teamable\ValueObjects\TeamType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Gate;

trait HasTeams
{
    use HasDataRepository;

    public function teams(?TeamType $type = null): BelongsToMany
    {
        return $this->belongsToMany(
            related: Team::class,
            table: 'team_user',
            foreignPivotKey: 'user_id',
            relatedPivotKey: 'team_id'
        )
            // @phpstan-ignore-next-line
            ->when($type, function (Builder $query, TeamType $type) {
                $query->where('teamable_type', $type->class);
            })
            ->withTimestamps();
    }

    public function setCurrentTeam(Team $team): bool
    {
        $data = CurrentTeam::make(
            id: $team->id,
            teamType: $team->getType()
        );

        $this->data_repository()->add(
            data: $data,
            valuesAttributes: [
                'values->teamType' => $team->getType()->class,
            ]
        );

        return true;
    }

    public function getCurrentTeamId(TeamType $teamType): ?int
    {
        try {
            // @phpstan-ignore-next-line
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
        // @phpstan-ignore-next-line
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

        // @phpstan-ignore-next-line
        if (! $this->teams()
            ->get()
            ->contains($team)
        ) {
            $this->teams()->attach($team, $pivotData);

            //event(new UserJoinedTeam($this, $team));

            /*
            if ($this->relationLoaded($team->getType()->relation())) {
                $this->load($team->getType()->relation());
            }
            */

            $this->load('teams');

            if (is_null($this->getCurrentTeamId($team->type))) {
                $this->switchToTeam($team);
            }
        }

        return $this;
    }

    public function detachTeam(Team $team): self
    {
        $this->teams()->detach($team);

        //event(new UserLeftTeam($this, $team));
        /*
                if ($this->relationLoaded($team->getType()->relation())) {
                    $this->load($team->getType()->relation());
                }
        */
        $this->load('teams');

        if ($this->teams($team->getType())->count() === 0
            || $this->getCurrentTeamId($team->getType()) === $team->id
        ) {
            $this->clearCurrentTeamId($team->getType());

            $this->load('teams');
        }

        return $this;
    }

    public function switchToTeam(Team|int $team): self
    {
        if (is_int($team)) {
            $team = Team::findOrFail($team);
        }

        Gate::authorize('switch-team', [$team, $this]);

        $this->setCurrentTeam($team);

        if ($this->relationLoaded('teams')) {
            $this->load('teams');
        }

        return $this;
    }
}
