# Architecture

## Directory Structure

app/
  Http/
    Controllers/Api/V1/   # One controller per resource, thin
    Requests/             # FormRequest classes, one per action
    Resources/            # API Resources for all responses
    Middleware/           # Auth and role enforcement only
  Services/               # All business logic, one service per domain
  Models/                 # Eloquent models, relationships, scopes, casts
  Enums/                  # PHP 8.1+ backed enums (roles, payment methods)

## Request Lifecycle

Route → Middleware → FormRequest → Controller → Service → Model → Resource

Each layer has one responsibility. Nothing bleeds into another.

## Dependency Rules

- Controllers depend on Services and FormRequests only
- Services depend on Models and other Services only
- Models never depend on Services or Controllers
- Middleware handles auth and role checks only — no business logic
- FormRequests handle validation and authorization only

## What NOT To Do

- Do not put business logic in controllers, models, or middleware
- Do not instantiate Services with `new` — inject via constructor
- Do not use `DB::` facade where Eloquent can handle it
- Do not create god-services that handle multiple domains
- Do not skip FormRequest for "simple" endpoints
- Do not use `env()` outside of config files
