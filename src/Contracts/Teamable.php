<?php

namespace IBroStudio\Teamable\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface Teamable
{
    public function team(): MorphOne;
}
