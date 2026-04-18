# Boost Guidelines

A [Laravel Boost](https://github.com/laravel/boost) plugin that auto-discovers AI guidelines from installed Composer packages and merges them into your Boost context.

## Adding Guidelines

Any Composer package can provide AI guidelines — this includes regular packages, but also dedicated guideline-only repositories.

### In an existing package

Place Markdown files in `.ai/guidelines/` at the root of your package:

```
your-package/
└── .ai/guidelines/
    ├── general.md
    └── usage.md
```

### As a dedicated guidelines repository

You can create a Composer package that contains nothing but guideline files. This is useful for sharing organisation-wide conventions, team standards, or project-specific context across multiple applications:

```
your-org/guidelines/
└── .ai/guidelines/
    ├── php.md
    ├── architecture.md
    └── git.md
```

Require it as a dev dependency in any project that should inherit those guidelines:

```bash
composer require your-org/guidelines --dev
```

The guideline files are plain Markdown. Write them as instructions for an AI agent — describe conventions, gotchas, required patterns, or anything an agent should know when working in the project.

## The Problem

Laravel Boost loads AI context from `resources/boost/` in your application. But when you install a package, any guidelines that package author wrote for AI agents are not automatically surfaced — you'd have to manually copy them.

This package solves that by scanning all installed vendor packages for guideline files and automatically including them in your Boost context.

## How It Works

1. Packages provide Markdown guideline files in a well-known path (e.g. `.ai/guidelines/*.md`)
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
