<?php

namespace IBroStudio\Teamable\ValueObjects;

use InvalidArgumentException;
use MichaelRubel\ValueObjects\ValueObject;

final class CurrentTeam extends ValueObject
{
    protected int|array $id;

    protected TeamType $teamType;

    public function __construct(int|array $id, TeamType|string $teamType)
    {
        if (isset($this->id)) {
            throw new InvalidArgumentException(self::IMMUTABLE_MESSAGE);
        }

        $this->id = $id;
        $this->teamType = $teamType instanceof TeamType
            ? $teamType
            : TeamType::make($teamType);
    }

    public function value(): int|array
    {
        return $this->id;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'teamType' => $this->teamType->class,
        ];
    }
}
