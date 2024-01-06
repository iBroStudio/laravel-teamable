<?php

namespace IBroStudio\Teamable\ValueObjects;

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use MichaelRubel\ValueObjects\Collection\Primitive\Text;

final class TeamType extends Text
{
    public readonly string $class;

    public function __construct(string|Stringable $value)
    {
        $this->class = $value;

        parent::__construct(
            Str::of($value)
                ->afterLast('\\')
                ->lower()
        );
    }

    public function relation(): string
    {
        return Str::plural($this->value);
    }

    public function currentRelation(): string
    {
        return Str::of($this->value)
            ->ucfirst()
            ->prepend('current');
    }

    public function currentId(): string
    {
        return Str::of($this->value)
            ->prepend('current_')
            ->append('_id');
    }

    public function toArray(): array
    {
        return [
            'type' => $this->value(),
            'class' => $this->class,
        ];
    }
}
