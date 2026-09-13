<?php

declare(strict_types=1);

namespace Maarheeze\BoostGuidelines\Tests\Rendering;

use Maarheeze\BoostGuidelines\Discovery\GuidelineFile;
use Maarheeze\BoostGuidelines\Discovery\GuidelinesDiscoverer;
use Maarheeze\BoostGuidelines\Rendering\Mode;
use Maarheeze\BoostGuidelines\Rendering\PlanBuilder;
use Maarheeze\BoostGuidelines\Tests\FilesystemTestCase;

use function array_keys;
use function array_map;
use function sprintf;

class PlanBuilderTest extends FilesystemTestCase
{
    public function testAlwaysFilesAreInlined(): void
    {
        $tempDir = $this->createTempDir();
        $critical = 'vendor/org/package/.ai/guidelines/always/critical.md';
        $php = 'vendor/org/package/.ai/guidelines/php.md';
        $this->createFile($tempDir, $critical, "# Critical\n");
        $this->createFile($tempDir, $php, "# PHP\n\n**Globs:** `app/**`\n");

        $discoverer = $this->createStub(GuidelinesDiscoverer::class);
        $discoverer->method('discover')->willReturn([
            'org/package' => [
                sprintf('%s/%s', $tempDir, $critical),
                sprintf('%s/%s', $tempDir, $php),
            ],
        ]);

        $plan = new PlanBuilder($discoverer, $tempDir, Mode::Index)->plan();

        $this->deleteDirectory($tempDir);

        $this->assertSame(['org/package'], array_keys($plan->always));
        $this->assertSame([$critical], array_map(
            static function (GuidelineFile $file): string {
                return $file->relativePath;
            },
            $plan->always['org/package'],
        ));
        $this->assertSame([], $plan->inlined);
    }

    public function testFileWithGlobsBecomesIndexRow(): void
    {
        $tempDir = $this->createTempDir();
        $php = 'vendor/org/package/.ai/guidelines/php.md';
        $this->createFile($tempDir, $php, "# PHP\n\n**Globs:** `app/**`, `tests/**`\n");

        $discoverer = $this->createStub(GuidelinesDiscoverer::class);
        $discoverer->method('discover')->willReturn([
            'org/package' => [sprintf('%s/%s', $tempDir, $php)],
        ]);

        $plan = new PlanBuilder($discoverer, $tempDir, Mode::Index)->plan();

        $this->deleteDirectory($tempDir);

        $this->assertCount(1, $plan->rows);
        $this->assertSame($php, $plan->rows[0]->relativePath);
        $this->assertSame(['app/**', 'tests/**'], $plan->rows[0]->globs);
        $this->assertSame('`app/**`, `tests/**`', $plan->rows[0]->globsAsMarkdown());
        $this->assertSame([], $plan->inlined);
    }

    public function testPackageWithIndexEmitsPointerOnly(): void
    {
        $tempDir = $this->createTempDir();
        $index = 'vendor/org/package/.ai/guidelines/index.md';
        $php = 'vendor/org/package/.ai/guidelines/php.md';
        $filament = 'vendor/org/package/.ai/guidelines/filament.md';
        $this->createFile($tempDir, $index, "# Index\n");
        $this->createFile($tempDir, $php, "# PHP\n");
        $this->createFile(
            $tempDir,
            $filament,
            "# Filament\n\n**Globs:** `app/Filament/**`\n",
        );

        $discoverer = $this->createStub(GuidelinesDiscoverer::class);
        $discoverer->method('discover')->willReturn([
            'org/package' => [
                sprintf('%s/%s', $tempDir, $index),
                sprintf('%s/%s', $tempDir, $php),
                sprintf('%s/%s', $tempDir, $filament),
            ],
        ]);

        $plan = new PlanBuilder($discoverer, $tempDir, Mode::Index)->plan();

        $this->deleteDirectory($tempDir);

        $this->assertSame(['org/package' => $index], $plan->pointers);
        $this->assertSame([], $plan->rows);
        $this->assertSame([], $plan->inlined);
    }

    public function testFileWithoutGlobsOrIndexIsInlinedAndNamed(): void
    {
        $tempDir = $this->createTempDir();
        $php = 'vendor/org/package/.ai/guidelines/php.md';
        $this->createFile($tempDir, $php, "# PHP\n");

        $discoverer = $this->createStub(GuidelinesDiscoverer::class);
        $discoverer->method('discover')->willReturn([
            'org/package' => [sprintf('%s/%s', $tempDir, $php)],
        ]);

        $plan = new PlanBuilder($discoverer, $tempDir, Mode::Index)->plan();

        $this->deleteDirectory($tempDir);

        $this->assertSame([], $plan->rows);
        $this->assertSame([], $plan->pointers);
        $this->assertSame(['org/package'], array_keys($plan->inlined));
    }

    public function testInlineModeInlinesEverything(): void
    {
        $tempDir = $this->createTempDir();
        $index = 'vendor/org/package/.ai/guidelines/index.md';
        $php = 'vendor/org/package/.ai/guidelines/php.md';
        $this->createFile($tempDir, $index, "# Index\n");
        $this->createFile($tempDir, $php, "# PHP\n\n**Globs:** `app/**`\n");

        $discoverer = $this->createStub(GuidelinesDiscoverer::class);
        $discoverer->method('discover')->willReturn([
            'org/package' => [
                sprintf('%s/%s', $tempDir, $index),
                sprintf('%s/%s', $tempDir, $php),
            ],
        ]);

        $plan = new PlanBuilder($discoverer, $tempDir, Mode::Inline)->plan();

        $this->deleteDirectory($tempDir);

        $this->assertSame([], $plan->rows);
        $this->assertSame([], $plan->pointers);
        $this->assertFalse($plan->isIndexMode());
        $this->assertSame([$index, $php], array_map(
            static function (GuidelineFile $file): string {
                return $file->relativePath;
            },
            $plan->inlined['org/package'],
        ));
    }
}
