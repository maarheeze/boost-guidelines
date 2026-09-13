# Boost Guidelines

[Laravel Boost](https://github.com/laravel/boost) loads AI context from your application's own files. But when you install a Composer package, any guidelines that package ships for AI agents don't appear automatically — you'd have to copy them by hand.

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
    ├── always/
    │   └── critical.md
    ├── index.md
    ├── general.md
    └── usage.md
```

Write plain Markdown — describe conventions, gotchas, required patterns, or anything an AI agent should know when working with your package.

By default your guidelines are delivered as an *index* rather than inlined into every session, so the agent reads a file at the moment it is about to touch a matching path. Three things decide how each file is delivered:

### `always/`

Files in an `always/` sub-directory are inlined verbatim into the agent's context, in every session. Reserve this for the handful of rules that must be present before the agent does anything at all.

### The globs line

Any other file declares the paths it applies to with a globs line in its header:

```markdown
# Filament

**Globs:** `app/Filament/**`, `resources/views/filament/**`
```

The file is then listed as a single row in the index, and the agent opens it when it is about to work on a matching path.

### `index.md`

If your guidelines directory contains an `index.md`, the plugin emits one pointer to that file and nothing else from your package, apart from `always/` files, which are always inlined — you own the mapping, and it is your responsibility to keep the index complete.

A file with no globs line, in a package with no `index.md`, cannot be pulled on demand, so it is inlined in full under its package heading, below the index.

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
3. The discovered guidelines are rendered into your Boost context — `always/` files verbatim, everything else as an index of globs and file paths
4. Your AI agent (Claude Code, Cursor, etc.) reads the index automatically via Boost, and opens the file it points to when it is about to touch a matching path

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

    'mode' => 'index',

    'paths' => [
        '.ai/guidelines',
    ],

    'only' => [
        // 'maarheeze/guidelines',
    ],

    'except' => [
        // 'some-vendor/some-package',
    ],

];
```

### `mode`

How guidelines are delivered.

- `index` (default) — only `always/` files are inlined. Everything else becomes an index the agent reads on demand. Typically fifteen lines of context instead of several hundred.
- `inline` — every discovered file is rendered in full, in every session. The previous behaviour.

```php
'mode' => 'index',
```

### `paths`

A list of sub-paths within each vendor package to scan for Markdown guideline files. All `*.md` files found inside a matching directory are included, along with any in an `always/` sub-directory beneath it.

A file counts as an always file when the directory holding it is named `always`, wherever that directory sits. Pointing an entry here at a path whose own last segment is `always` therefore inlines every file it contains verbatim into every session.

Boost discovers `resources/boost/guidelines/` in installed packages on its own, so that path does not belong here. Add entries only for conventions Boost does not know about:

```php
'paths' => [
    '.ai/guidelines',
    'docs/ai',
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
│       ├── always/
│       │   └── critical.md
│       └── php.md
└── acme/helpers/
    └── .ai/guidelines/
        └── laravel.md
```

Where `php.md` declares a globs line of `app/**` and `laravel.md` declares none, the generated block is:

```markdown
# maarheeze/guidelines Guidelines

<contents of always/critical.md>

# Guidelines from installed packages

Read these when you are about to touch a matching path — before creating or editing the file, not after.

| Globs | File |
|---|---|
| `app/**` | vendor/maarheeze/guidelines/.ai/guidelines/php.md |

These files ship no glob metadata, so they are included in full:

# acme/helpers Guidelines

<contents of laravel.md>
```
