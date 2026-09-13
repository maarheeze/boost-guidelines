<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Delivery Mode
    |--------------------------------------------------------------------------
    |
    | How discovered guidelines are rendered into the Boost context. In "index"
    | mode only files under an "always" sub-directory are inlined; everything
    | else is listed as a pointer the agent reads when it touches a matching
    | path. Use "inline" to render every discovered file verbatim instead.
    |
    | Supported: "index", "inline"
    |
    */

    'mode' => 'index',

    /*
    |--------------------------------------------------------------------------
    | Guidelines Paths
    |--------------------------------------------------------------------------
    |
    | A list of sub-paths within each vendor package that will be scanned for
    | Markdown guideline files. Each path is relative to the package root.
    | All *.md files found in matching directories will be included, plus any
    | in an "always" sub-directory beneath them.
    |
    | Boost already discovers "resources/boost/guidelines" in installed
    | packages by itself, so there is no need to list it here.
    |
    */

    'paths' => [
        '.ai/guidelines',
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
