# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Backend API for an agricultural marketplace platform in Côte d'Ivoire.
POS operators manage farmer accounts, place product orders on their behalf,
and record commodity repayments against credit debts.

## Tech Stack

- **Framework**: Laravel 11, PHP 8.2
- **Database**: MySQL
- **Auth**: Laravel Sanctum (token-based)
- **API only** — no frontend, no Blade views. Consumed by a Flutter mobile app (separate repo)

## Common Commands

```bash
# Start development server (with queue listener and log tail)
composer run dev

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Run all tests
php artisan test

# Run a single test file
php artisan test tests/Feature/ExampleTest.php

# Run a single test method
php artisan test --filter test_method_name

# Format code (Laravel Pint)
./vendor/bin/pint

# Interactive REPL
php artisan tinker
```

## Architecture

**Request Lifecycle**:
Route → Middleware → FormRequest (validation + auth) → Controller → Service → Model

**Controllers**: Thin. Validate via FormRequest, delegate to Service, return Resource.
One controller per resource. Extend `App\Http\Controllers\Controller`.

**Services**: All business logic lives here. Injected via constructor.
Never depend on Request or HTTP-layer objects.

**Responses**: Always use Laravel API Resources. Never return raw Eloquent models.

**Auth**: Protect routes with `middleware('auth:sanctum')`. Issue tokens via `$user->createToken(...)`.

**Roles**: Single `role` enum column on users table (admin / supervisor / operator).
Enforced via middleware.

**Testing**: PHPUnit 11. Feature tests extend `Tests\TestCase`. Use `RefreshDatabase` trait to reset the database between tests.

## Reference Docs

For detailed standards, read the relevant file before starting work:

- `.claude/rules/architecture.md` — directory structure, dependency rules
- `.claude/rules/coding-style.md` — controllers, models, enums, SOLID/DRY/KISS
- `.claude/rules/database-schema.md` — full schema + Eloquent relationships
- `.claude/rules/business-rules.md` — FIFO, credit limit, interest, commodity rate
- `.claude/rules/api-conventions.md` — response format, status codes, versioning

## Key Conventions

- PHP 8.2+ — use typed properties, enums, and readonly classes where appropriate.
- `declare(strict_types=1)` on every PHP file
- Prefix all routes: `/api/v1/`
- Use `decimal(10,2)` for all monetary and weight values
- Use Laravel's `foreignId()->constrained()->cascadeOnDelete()` for all foreign keys

## Hard Rules

- Never put business logic in controllers, models, or middleware
- Never return raw Eloquent models — always use API Resources
- Never use `request()->all()` or skip FormRequest validation
- Never modify existing migrations that have already been run
- Never use `env()` outside of config files
- Never install new packages without asking first
- Never guess on business logic (credit limit, FIFO, interest) — read `.claude/rules/business-rules.md`
- `dd()`, `dump()`, `ray()` must never appear in committed code
- Update `postman_collection.json` whenever new endpoints are added or modified

## Git Workflow

- Always create a new branch for each feature or bug fix
- Branch naming: `feature/short-description`, `fix/short-description`
- Always run `./vendor/bin/pint` before committing
- Commit messages follow conventional commits format:
  `feat:`, `fix:`, `chore:`, `refactor:`, `test:`
- One commit per logical unit of work — no "WIP" or "misc fixes" commits
- Write clear, descriptive commit messages
- Never commit code that breaks existing tests
- Don't mention "Co-Authored-By Claude" in commit messages

## Progress Tracking

- Refer to `progress.md` for current status and TODO items
- Update `progress.md` as you complete tasks
- Mark completed items with [x]
