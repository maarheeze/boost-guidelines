<?php

declare(strict_types=1);

namespace Maarheeze\BoostGuidelines\Facades;

use Illuminate\Support\Facades\Facade;
use Maarheeze\BoostGuidelines\Discovery\GuidelinesDiscoverer;

/**
 * @method static array<string, array<int, string>> discover()
 */
class Guidelines extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GuidelinesDiscoverer::class;
    }
}
