<?php

declare(strict_types=1);

namespace Maarheeze\BoostGuidelines\Discovery;

use function in_array;
use function ksort;

class GuidelinesDiscoverer
{
    /**
     * @param array<string> $only
     * @param array<string> $except
     */
    public function __construct(
        private readonly PackageScanner $scanner,
        private readonly array $only,
        private readonly array $except,
    ) {
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function discover(): array
    {
        $grouped = [];

        foreach ($this->scanner->scan() as $package => $files) {
            if (!$this->isAllowed($package)) {
                continue;
            }

            $grouped[$package] = $files;
        }

        ksort($grouped);

        return $grouped;
    }

    private function isAllowed(string $package): bool
    {
        if ($this->only !== [] && !in_array($package, $this->only, true)) {
            return false;
        }

        return !in_array($package, $this->except, true);
    }
}
