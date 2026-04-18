<?php

declare(strict_types=1);

namespace Maarheeze\BoostGuidelines\Discovery;

use function explode;
use function glob;
use function sprintf;
use function strlen;
use function substr;

class PackageScanner
{
    /**
     * @param array<string> $paths
     */
    public function __construct(
        private readonly string $vendorPath,
        private readonly array $paths,
    ) {
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function scan(): array
    {
        $files = [];

        foreach ($this->paths as $path) {
            foreach ($this->scanPath($path) as $file) {
                $files[] = $file;
            }
        }

        return $this->groupByPackage($files);
    }

    /**
     * @return array<int, string>
     */
    private function scanPath(string $path): array
    {
        return glob(sprintf('%s/*/*/%s/*.md', $this->vendorPath, $path)) ?: [];
    }

    /**
     * @param array<int, string> $files
     *
     * @return array<string, array<int, string>>
     */
    private function groupByPackage(array $files): array
    {
        $grouped = [];

        foreach ($files as $file) {
            $package = $this->extractPackageName($file);
            $grouped[$package][] = $file;
        }

        return $grouped;
    }

    private function extractPackageName(string $file): string
    {
        $relative = substr($file, strlen($this->vendorPath) + 1);
        $parts = explode('/', $relative);

        return sprintf('%s/%s', $parts[0], $parts[1]);
    }
}
