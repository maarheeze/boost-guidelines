<?php

declare(strict_types=1);

namespace Maarheeze\BoostGuidelines\Tests;

use PHPUnit\Framework\TestCase;

use function dirname;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function rmdir;
use function scandir;
use function sprintf;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

abstract class FilesystemTestCase extends TestCase
{
    protected function createTempDir(): string
    {
        $path = sprintf('%s/boost-guidelines-%s', sys_get_temp_dir(), uniqid());
        mkdir($path, 0777, true);

        return $path;
    }

    protected function createFile(string $tempDir, string $relativePath): void
    {
        $absolutePath = sprintf('%s/%s', $tempDir, $relativePath);
        $directory = dirname($absolutePath);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($absolutePath, '');
    }

    protected function deleteDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        foreach (scandir($path) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $entryPath = sprintf('%s/%s', $path, $entry);

            if (is_dir($entryPath)) {
                $this->deleteDirectory($entryPath);
            } else {
                unlink($entryPath);
            }
        }

        rmdir($path);
    }
}
