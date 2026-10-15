# AGENTS.md

## Project

Ginfo — PHP library for parsing system info (CPU, memory, disks, network, processes, sensors, PHP, web servers, databases). Linux-only, reads `/proc`, `/sys`, `/etc` and external tools.

- PHP >= 8.2
- PSR-4: `Ginfo\` → `src/`, `Ginfo\Tests\` → `tests/`
- Entry point: `src/Ginfo.php` (aggregator, accepts custom parsers via `InfoParserInterface`)
- Parsers: `src/Parser/`, value objects: `src/Info/` (`InfoInterface`), OS layer: `src/Os/`

## Commands

```bash
composer install
vendor/bin/phpunit
vendor/bin/phpstan analyse
PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix --dry-run --diff
PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix
```

Tests require Linux with readable `/proc`, `/sys`, `/etc`; some tests need running services (MySQL, Postgres, Redis, etc.) and PHP extensions.

## Code style

- Strict types (`declare(strict_types=1)`), `final readonly` classes where possible, constructor property promotion.
- Keep parsers small and side-effect free; return `null` when data source is unavailable instead of throwing.
- Use `Symfony\Component\Process` for external commands, never raw `shell_exec`.
- Add new parsers as `Parser/*Parser.php` + `Info/*Info.php` pair, register in `Ginfo.php`.
- No UI, no i18n, no Windows/BSD support.

## Conventions

- Commit messages follow [Conventional Commits](https://www.conventionalcommits.org/) (`feat:`, `fix:`, `build:`, `chore:`, ...).
