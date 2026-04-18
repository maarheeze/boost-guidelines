<?php

declare(strict_types=1);

namespace Maarheeze\BoostGuidelines\Tests\Discovery;

use Maarheeze\BoostGuidelines\Discovery\PackageScanner;
use Maarheeze\BoostGuidelines\Tests\FilesystemTestCase;

use function sprintf;

class PackageScannerTest extends FilesystemTestCase
{
    public function testScanReturnsFilesGroupedByPackage(): void
    {
        $tempDir = $this->createTempDir();
        $this->createFile($tempDir, 'org/package-a/.ai/guidelines/general.md');
        $this->createFile($tempDir, 'org/package-b/.ai/guidelines/general.md');

        $result = (new PackageScanner(
            vendorPath: $tempDir,
            paths: ['.ai/guidelines'],
        ))->scan();

        $this->deleteDirectory($tempDir);

        $this->assertArrayHasKey('org/package-a', $result);
        $this->assertArrayHasKey('org/package-b', $result);
    }

    public function testScanReturnsEmptyArrayWhenNoFilesFound(): void
    {
        $tempDir = $this->createTempDir();

        $result = (new PackageScanner(
            vendorPath: $tempDir,
            paths: ['.ai/guidelines'],
        ))->scan();

        $this->deleteDirectory($tempDir);

        $this->assertSame([], $result);
    }

    public function testScanReturnsAbsoluteFilePaths(): void
    {
        $tempDir = $this->createTempDir();
        $this->createFile($tempDir, 'org/package-a/.ai/guidelines/general.md');

        $result = (new PackageScanner(
            vendorPath: $tempDir,
            paths: ['.ai/guidelines'],
        ))->scan();

        $this->deleteDirectory($tempDir);

        $this->assertContains(
            sprintf('%s/org/package-a/.ai/guidelines/general.md', $tempDir),
            $result['org/package-a'],
        );
    }

    public function testScanSearchesMultiplePaths(): void
    {
        $tempDir = $this->createTempDir();
        $this->createFile($tempDir, 'org/package-a/.ai/guidelines/general.md');
        $this->createFile($tempDir, 'org/package-b/resources/boost/guidelines/general.md');

        $result = (new PackageScanner(
            vendorPath: $tempDir,
            paths: ['.ai/guidelines', 'resources/boost/guidelines'],
        ))->scan();

        $this->deleteDirectory($tempDir);

        $this->assertArrayHasKey('org/package-a', $result);
        $this->assertArrayHasKey('org/package-b', $result);
    }
}
