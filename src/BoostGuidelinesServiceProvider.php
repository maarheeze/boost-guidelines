<?php

declare(strict_types=1);

namespace Maarheeze\BoostGuidelines;

use Illuminate\Support\ServiceProvider;

use function resource_path;

class BoostGuidelinesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../resources/boost/guidelines' => resource_path('boost/guidelines'),
        ], 'laravel-assets');
    }
}
