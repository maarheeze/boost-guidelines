<?php

declare(strict_types=1);

namespace Maarheeze\BoostGuidelines\Rendering;

use function array_map;
use function implode;
use function sprintf;

readonly class IndexRow
{
    /**
     * @param array<int, string> $globs
     */
    public function __construct(
        public string $relativePath,
        public array $globs,
    ) {
    }

    public function globsAsMarkdown(): string
    {
        $quoted = array_map(
            static function (string $glob): string {
                return sprintf('`%s`', $glob);
            },
            $this->globs,
        );

        return implode(', ', $quoted);
    }
}
