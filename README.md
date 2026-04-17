# Boost Guidelines

Auto-discovers and loads `.ai/guidelines/` from all installed packages into Laravel Boost.

## What This Does

Without this package, Laravel Boost only loads guidelines from `resources/boost/guidelines/` in packages.

This package enables Boost to **automatically discover and load guidelines from any package's `.ai/guidelines/` folder**, making guidelines truly agent-agnostic while keeping Boost integration seamless.

## Installation

```bash
composer require maarheeze/boost-guidelines --dev
php artisan boost:install
php artisan boost:update --discover
```

## How It Works

1. Any package can now provide guidelines in `.ai/guidelines/*.md`
2. This package discovers all `.ai/guidelines/` folders in `vendor/`
3. Guidelines are grouped by package and merged into `CLAUDE.md`
4. Claude Code reads the final `CLAUDE.md` automatically

## Example

If you have:
- `maarheeze/guidelines` with `.ai/guidelines/php.md`
- `some-vendor/guidelines` with `.ai/guidelines/laravel.md`

Both will be auto-loaded and available to all AI agents (like Claude Code, Cursor, etc.).