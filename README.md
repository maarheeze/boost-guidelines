# Boost Guidelines

Laravel Boost loads AI context from your application's own files. But when you install a Composer package, any guidelines that package ships for AI agents don't appear automatically — you'd have to copy them by hand.

This plugin solves that. It scans all installed vendor packages for guideline files and merges them into your Boost context automatically.

## Why `.ai/guidelines/`

Laravel Boost's built-in path for third-party guidelines is `resources/boost/guidelines/` — a Boost-specific convention. This plugin adds support for `.ai/guidelines/`, a tool-agnostic path that many AI tools (Claude Code, Cursor, and others) already understand.

By using `.ai/guidelines/`, package authors write their AI guidelines once in a neutral location — and those guidelines work across all supporting tools, not just Boost.

## For Application Developers

Install once:

```bash
composer require maarheeze/boost-guidelines --dev
php artisan boost:install
php artisan boost:update --discover
```

Any installed package that ships `.ai/guidelines/*.md` files will now be discovered automatically and merged into your Boost context. No further configuration required.

## For Package Authors

Add a `.ai/guidelines/` directory at the root of your package:

```
your-package/
└── .ai/guidelines/
    ├── general.md
    └── usage.md
```

Write plain Markdown — describe conventions, gotchas, required patterns, or anything an AI agent should know when working with your package. Any application with this plugin installed will pick these up automatically.

## For Teams: Shared Guidelines Repository

You can create a Composer package that contains nothing but guideline files, and require it as a dev dependency in every project that should inherit those guidelines:

```
your-org/guidelines/
└── .ai/guidelines/
    ├── php.md
    ├── architecture.md
    └── git.md
```

```bash
composer require your-org/guidelines --dev
```

This is the cleanest way to share organisation-wide conventions, team standards, or cross-project context — version-controlled and distributed through Composer like any other dependency.

## How It Works

1. Packages provide Markdown guideline files in a well-known path (`.ai/guidelines/*.md`)
2. This plugin scans all installed vendor packages for matching files
3. The discovered guidelines are grouped by package and rendered into your Boost context
4. Your AI agent (Claude Code, Cursor, etc.) reads them automatically via Boost

## Installation

```bash
composer require maarheeze/boost-guidelines --dev
php artisan boost:install
php artisan boost:update --discover
```

To publish the configuration file:

```bash
php artisan vendor:publish --tag=boost-guidelines-config
```

## Configuration

After publishing, edit `config/boost-guidelines.php`:

```php
return [

    'paths' => [
        '.ai/guidelines',
        'resources/boost',
    ],

    'only' => [
        // 'maarheeze/guidelines',
    ],

    'except' => [
        // 'some-vendor/some-package',
    ],

];
```

### `paths`

A list of sub-paths within each vendor package to scan for Markdown guideline files. All `*.md` files found inside a matching directory are included.

You can add multiple paths to support packages that use different conventions:

```php
'paths' => [
    '.ai/guidelines',
    'resources/boost',
],
```

### `only`

An allowlist of packages. When non-empty, only guidelines from the listed packages are loaded. All others are ignored. Use the full `vendor/package` name.

```php
'only' => [
    'maarheeze/guidelines',
    'acme/laravel-helpers',
],
```

### `except`

A blocklist of packages. Guidelines from listed packages are always excluded, even if they match a configured path. Applied after `only`.

```php
'except' => [
    'some-vendor/noisy-package',
],
```

## Example

Given these installed packages:

```
vendor/
├── maarheeze/guidelines/
│   └── .ai/guidelines/
│       └── php.md
└── acme/helpers/
    └── .ai/guidelines/
        └── laravel.md
```

Both `php.md` and `laravel.md` are discovered and merged into your Boost context, grouped under their respective package name.
