<?php

declare(strict_types=1);

namespace Maarheeze\BoostGuidelines\Discovery;

use function array_filter;
use function array_map;
use function array_slice;
use function array_values;
use function basename;
use function dirname;
use function explode;
use function file_get_contents;
use function implode;
use function max;
use function preg_match;
use function str_split;
use function str_starts_with;
use function strlen;
use function substr;
use function trim;

class GuidelineFile
{
    private const string GLOBS_PATTERN = '/^\*{0,2}Globs:\*{0,2}[ \t]*(.+)$/mi';
    private const int HEADER_LINES = 10;

    /**
     * @param array<int, string> $globs
     */
    private function __construct(
        public readonly string $path,
        public readonly string $relativePath,
        public readonly bool $isAlways,
        public readonly array $globs,
    ) {
    }

    public static function fromPath(string $path, string $basePath): self
    {
        return new self(
            path: $path,
            relativePath: self::relativePath($path, $basePath),
            isAlways: basename(dirname($path)) === 'always',
            globs: self::parseGlobs($path),
        );
    }

    public function isIndex(): bool
    {
        return !$this->isAlways && basename($this->path) === 'index.md';
    }

    public function hasGlobs(): bool
    {
        return $this->globs !== [];
    }

    private static function relativePath(string $path, string $basePath): string
    {
        $prefix = $basePath . '/';

        if (!str_starts_with($path, $prefix)) {
            return $path;
        }

        return substr($path, strlen($prefix));
    }

    /**
     * @return array<int, string>
     */
    private static function parseGlobs(string $path): array
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            return [];
        }

        $header = implode("\n", array_slice(explode("\n", $contents), 0, self::HEADER_LINES));

        if (preg_match(self::GLOBS_PATTERN, $header, $matches) !== 1) {
            return [];
        }

        $globs = array_map(
            static function (string $glob): string {
                return trim($glob, " \t\r\n`\"'");
            },
            self::splitGlobs($matches[1]),
        );

        $globs = array_filter($globs, static function (string $glob): bool {
            return $glob !== '';
        });

        return array_values($globs);
    }

    /**
     * @return array<int, string>
     */
    private static function splitGlobs(string $line): array
    {
        $globs = [];
        $current = '';
        $depth = 0;

        foreach (str_split($line) as $character) {
            if ($character === ',' && $depth === 0) {
                $globs[] = $current;
                $current = '';

                continue;
            }

            if ($character === '{') {
                $depth++;
            } elseif ($character === '}') {
                $depth = max(0, $depth - 1);
            }

            $current .= $character;
        }

        $globs[] = $current;

        return $globs;
    }
}
