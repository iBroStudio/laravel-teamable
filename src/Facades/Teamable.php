<?php

namespace IBroStudio\Teamable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \IBroStudio\Teamable\Teamable
 */
class Teamable extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \IBroStudio\Teamable\Teamable::class;
    }
}
