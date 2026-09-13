<?php

declare(strict_types=1);

namespace Maarheeze\BoostGuidelines\Rendering;

use Maarheeze\BoostGuidelines\Discovery\GuidelineFile;
use Maarheeze\BoostGuidelines\Discovery\GuidelinesDiscoverer;

readonly class PlanBuilder
{
    public function __construct(
        private GuidelinesDiscoverer $discoverer,
        private string $basePath,
        private Mode $mode,
    ) {
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function discover(): array
    {
        return $this->discoverer->discover();
    }

    public function plan(): GuidelinesPlan
    {
        $always = [];
        $pointers = [];
        $rows = [];
        $inlined = [];

        foreach ($this->discoverer->discover() as $package => $paths) {
            $remaining = [];

            foreach ($paths as $path) {
                $file = GuidelineFile::fromPath($path, $this->basePath);

                if ($file->isAlways) {
                    $always[$package][] = $file;

                    continue;
                }

                $remaining[] = $file;
            }

            if ($this->mode === Mode::Inline) {
                if ($remaining !== []) {
                    $inlined[$package] = $remaining;
                }

                continue;
            }

            $index = $this->findIndex($remaining);

            if ($index instanceof GuidelineFile) {
                $pointers[$package] = $index->relativePath;

                continue;
            }

            foreach ($remaining as $file) {
                if ($file->hasGlobs()) {
                    $rows[] = new IndexRow($file->relativePath, $file->globs);

                    continue;
                }

                $inlined[$package][] = $file;
            }
        }

        return new GuidelinesPlan(
            mode: $this->mode,
            always: $always,
            pointers: $pointers,
            rows: $rows,
            inlined: $inlined,
        );
    }

    /**
     * @param array<int, GuidelineFile> $files
     */
    private function findIndex(array $files): ?GuidelineFile
    {
        foreach ($files as $file) {
            if ($file->isIndex()) {
                return $file;
            }
        }

        return null;
    }
}
