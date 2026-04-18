<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Guidelines Paths
    |--------------------------------------------------------------------------
    |
    | A list of sub-paths within each vendor package that will be scanned for
    | Markdown guideline files. Each path is relative to the package root.
    | All *.md files found in matching directories will be included.
    |
    */

    'paths' => [
        '.ai/guidelines',
        'resources/boost',
    ],

    /*
    |--------------------------------------------------------------------------
    | Package Allowlist
    |--------------------------------------------------------------------------
    |
    | When non-empty, only guidelines from the listed packages will be loaded.
    | Use the full vendor/package name (e.g. "maarheeze/guidelines").
    | Takes precedence over the blocklist below.
    |
    */

    'only' => [
        // 'maarheeze/guidelines',
    ],

    /*
    |--------------------------------------------------------------------------
    | Package Blocklist
    |--------------------------------------------------------------------------
    |
    | Guidelines from the listed packages will always be excluded, even if
    | they are present in the configured paths. Use the full vendor/package
    | name (e.g. "some-vendor/some-package").
    |
    */

    'except' => [
        // 'some-vendor/some-package',
    ],

];
