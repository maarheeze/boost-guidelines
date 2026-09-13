<?php

declare(strict_types=1);

namespace Maarheeze\BoostGuidelines\Facades;

use Illuminate\Support\Facades\Facade;
use Maarheeze\BoostGuidelines\Rendering\GuidelinesPlan;
use Maarheeze\BoostGuidelines\Rendering\PlanBuilder;

/**
 * @method static array<string, array<int, string>> discover()
 * @method static GuidelinesPlan plan()
 */
class Guidelines extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PlanBuilder::class;
    }
}
