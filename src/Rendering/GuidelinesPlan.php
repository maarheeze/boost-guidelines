<?php

declare(strict_types=1);

namespace Maarheeze\BoostGuidelines\Rendering;

use Maarheeze\BoostGuidelines\Discovery\GuidelineFile;

use function array_key_exists;

readonly class GuidelinesPlan
{
    /**
     * @param array<string, array<int, GuidelineFile>> $always
     * @param array<string, string>                    $pointers
     * @param array<int, IndexRow>                     $rows
     * @param array<string, array<int, GuidelineFile>> $inlined
     */
    public function __construct(
        public Mode $mode,
        public array $always,
        public array $pointers,
        public array $rows,
        public array $inlined,
    ) {
    }

    public function isIndexMode(): bool
    {
        return $this->mode === Mode::Index;
    }

    public function hasIndex(): bool
    {
        return $this->rows !== [] || $this->pointers !== [];
    }

    public function hasAlways(string $package): bool
    {
        return array_key_exists($package, $this->always);
    }
}
