<?php

declare(strict_types=1);

namespace Maarheeze\BoostGuidelines;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Maarheeze\BoostGuidelines\Discovery\GuidelinesDiscoverer;
use Maarheeze\BoostGuidelines\Discovery\PackageScanner;
use Maarheeze\BoostGuidelines\Rendering\Mode;
use Maarheeze\BoostGuidelines\Rendering\PlanBuilder;

use function array_filter;
use function array_values;
use function is_array;
use function is_string;

class BoostGuidelinesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/boost-guidelines.php' => $this->app->configPath('boost-guidelines.php'),
        ], 'boost-guidelines-config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/boost-guidelines.php',
            'boost-guidelines',
        );

        $this->app->bind(GuidelinesDiscoverer::class, static function (Application $app): GuidelinesDiscoverer {
            $config = $app->make(Repository::class);

            return new GuidelinesDiscoverer(
                scanner: new PackageScanner(
                    vendorPath: $app->basePath('vendor'),
                    paths: self::configArray($config, 'boost-guidelines.paths'),
                ),
                only: self::configArray($config, 'boost-guidelines.only'),
                except: self::configArray($config, 'boost-guidelines.except'),
            );
        });

        $this->app->bind(PlanBuilder::class, static function (Application $app): PlanBuilder {
            $config = $app->make(Repository::class);

            return new PlanBuilder(
                discoverer: $app->make(GuidelinesDiscoverer::class),
                basePath: $app->basePath(),
                mode: self::configMode($config, 'boost-guidelines.mode'),
            );
        });
    }

    /**
     * @return array<string>
     */
    private static function configArray(Repository $config, string $key): array
    {
        $value = $config->get($key);

        if (!is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, is_string(...)));
    }

    private static function configMode(Repository $config, string $key): Mode
    {
        $value = $config->get($key);

        if (!is_string($value)) {
            return Mode::Index;
        }

        $mode = Mode::tryFrom($value);

        if ($mode === null) {
            return Mode::Index;
        }

        return $mode;
    }
}
