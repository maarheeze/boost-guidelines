<?php

declare(strict_types=1);

namespace Maarheeze\BoostGuidelines\Tests\Discovery;

use Maarheeze\BoostGuidelines\Discovery\GuidelinesDiscoverer;
use Maarheeze\BoostGuidelines\Discovery\PackageScanner;
use PHPUnit\Framework\TestCase;

use function array_keys;

class GuidelinesDiscovererTest extends TestCase
{
    public function testDiscoverReturnsAllPackagesWhenNoFiltersAreSet(): void
    {
        $scanner = $this->createMock(PackageScanner::class);
        $scanner->expects($this->once())
            ->method('scan')
            ->willReturn([
                'org/package-a' => ['/vendor/org/package-a/.ai/guidelines/general.md'],
                'org/package-b' => ['/vendor/org/package-b/.ai/guidelines/general.md'],
            ]);

        $result = (new GuidelinesDiscoverer(
            scanner: $scanner,
            only: [],
            except: [],
        ))->discover();

        $this->assertArrayHasKey('org/package-a', $result);
        $this->assertArrayHasKey('org/package-b', $result);
    }

    public function testDiscoverReturnsEmptyArrayWhenScannerFindsNoFiles(): void
    {
        $scanner = $this->createMock(PackageScanner::class);
        $scanner->expects($this->once())
            ->method('scan')
            ->willReturn([]);

        $result = (new GuidelinesDiscoverer(
            scanner: $scanner,
            only: [],
            except: [],
        ))->discover();

        $this->assertSame([], $result);
    }

    public function testDiscoverResultsAreSortedAlphabeticallyByPackage(): void
    {
        $scanner = $this->createMock(PackageScanner::class);
        $scanner->expects($this->once())
            ->method('scan')
            ->willReturn([
                'org/zebra' => ['/vendor/org/zebra/.ai/guidelines/general.md'],
                'org/alpha' => ['/vendor/org/alpha/.ai/guidelines/general.md'],
                'org/middle' => ['/vendor/org/middle/.ai/guidelines/general.md'],
            ]);

        $result = (new GuidelinesDiscoverer(
            scanner: $scanner,
            only: [],
            except: [],
        ))->discover();

        $this->assertSame(['org/alpha', 'org/middle', 'org/zebra'], array_keys($result));
    }

    public function testOnlyFilterIncludesOnlyListedPackages(): void
    {
        $scanner = $this->createMock(PackageScanner::class);
        $scanner->expects($this->once())
            ->method('scan')
            ->willReturn([
                'org/package-a' => ['/vendor/org/package-a/.ai/guidelines/general.md'],
                'org/package-b' => ['/vendor/org/package-b/.ai/guidelines/general.md'],
            ]);

        $result = (new GuidelinesDiscoverer(
            scanner: $scanner,
            only: ['org/package-a'],
            except: [],
        ))->discover();

        $this->assertArrayHasKey('org/package-a', $result);
        $this->assertArrayNotHasKey('org/package-b', $result);
    }

    public function testExceptFilterExcludesListedPackages(): void
    {
        $scanner = $this->createMock(PackageScanner::class);
        $scanner->expects($this->once())
            ->method('scan')
            ->willReturn([
                'org/package-a' => ['/vendor/org/package-a/.ai/guidelines/general.md'],
                'org/package-b' => ['/vendor/org/package-b/.ai/guidelines/general.md'],
            ]);

        $result = (new GuidelinesDiscoverer(
            scanner: $scanner,
            only: [],
            except: ['org/package-b'],
        ))->discover();

        $this->assertArrayHasKey('org/package-a', $result);
        $this->assertArrayNotHasKey('org/package-b', $result);
    }

    public function testExceptExcludesPackageEvenWhenInOnly(): void
    {
        $scanner = $this->createMock(PackageScanner::class);
        $scanner->expects($this->once())
            ->method('scan')
            ->willReturn([
                'org/package-a' => ['/vendor/org/package-a/.ai/guidelines/general.md'],
            ]);

        $result = (new GuidelinesDiscoverer(
            scanner: $scanner,
            only: ['org/package-a'],
            except: ['org/package-a'],
        ))->discover();

        $this->assertArrayNotHasKey('org/package-a', $result);
    }
}
