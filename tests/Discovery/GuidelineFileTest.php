<?php

declare(strict_types=1);

namespace Maarheeze\BoostGuidelines\Tests\Discovery;

use Maarheeze\BoostGuidelines\Discovery\GuidelineFile;
use Maarheeze\BoostGuidelines\Tests\FilesystemTestCase;

use function sprintf;

class GuidelineFileTest extends FilesystemTestCase
{
    public function testParsesGlobsLine(): void
    {
        $tempDir = $this->createTempDir();
        $php = 'vendor/org/package/.ai/guidelines/php.md';
        $this->createFile(
            $tempDir,
            $php,
            "# PHP\n\n**Globs:** `app/**`, `database/**`, `tests/**`\n\nSome rules.\n",
        );

        $file = GuidelineFile::fromPath(sprintf('%s/%s', $tempDir, $php), $tempDir);

        $this->deleteDirectory($tempDir);

        $this->assertSame(['app/**', 'database/**', 'tests/**'], $file->globs);
        $this->assertTrue($file->hasGlobs());
    }

    public function testReturnsNoGlobsWhenLineIsAbsent(): void
    {
        $tempDir = $this->createTempDir();
        $php = 'vendor/org/package/.ai/guidelines/php.md';
        $this->createFile($tempDir, $php, "# PHP\n\nSome rules.\n");

        $file = GuidelineFile::fromPath(sprintf('%s/%s', $tempDir, $php), $tempDir);

        $this->deleteDirectory($tempDir);

        $this->assertSame([], $file->globs);
        $this->assertFalse($file->hasGlobs());
    }

    public function testIgnoresGlobsLineBelowTheHeader(): void
    {
        $tempDir = $this->createTempDir();
        $php = 'vendor/org/package/.ai/guidelines/php.md';
        $this->createFile(
            $tempDir,
            $php,
            "# PHP\n\n\n\n\n\n\n\n\n\n\n**Globs:** `app/**`\n",
        );

        $file = GuidelineFile::fromPath(sprintf('%s/%s', $tempDir, $php), $tempDir);

        $this->deleteDirectory($tempDir);

        $this->assertSame([], $file->globs);
    }

    public function testRelativePathIsResolvedAgainstBasePath(): void
    {
        $tempDir = $this->createTempDir();
        $php = 'vendor/org/package/.ai/guidelines/php.md';
        $this->createFile($tempDir, $php);

        $file = GuidelineFile::fromPath(sprintf('%s/%s', $tempDir, $php), $tempDir);

        $this->deleteDirectory($tempDir);

        $this->assertSame($php, $file->relativePath);
    }

    public function testDetectsAlwaysDirectory(): void
    {
        $tempDir = $this->createTempDir();
        $critical = 'vendor/org/package/.ai/guidelines/always/critical.md';
        $php = 'vendor/org/package/.ai/guidelines/php.md';
        $this->createFile($tempDir, $critical);
        $this->createFile($tempDir, $php);

        $always = GuidelineFile::fromPath(
            sprintf('%s/%s', $tempDir, $critical),
            $tempDir,
        );
        $regular = GuidelineFile::fromPath(sprintf('%s/%s', $tempDir, $php), $tempDir);

        $this->deleteDirectory($tempDir);

        $this->assertTrue($always->isAlways);
        $this->assertFalse($regular->isAlways);
    }

    public function testDetectsIndexFile(): void
    {
        $tempDir = $this->createTempDir();
        $index = 'vendor/org/package/.ai/guidelines/index.md';
        $php = 'vendor/org/package/.ai/guidelines/php.md';
        $this->createFile($tempDir, $index);
        $this->createFile($tempDir, $php);

        $indexFile = GuidelineFile::fromPath(
            sprintf('%s/%s', $tempDir, $index),
            $tempDir,
        );
        $regular = GuidelineFile::fromPath(sprintf('%s/%s', $tempDir, $php), $tempDir);

        $this->deleteDirectory($tempDir);

        $this->assertTrue($indexFile->isIndex());
        $this->assertFalse($regular->isIndex());
    }
}
