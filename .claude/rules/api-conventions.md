# API Conventions

## Versioning

All routes are prefixed with `/api/v1/`.

## Response Format

All responses follow this structure:

### Success

```json
{
  "success": true,
  "data": {},
  "message": "string"
}
```

### Error

```json
{
  "success": false,
  "message": "string",
  "errors": {}
}
```

## HTTP Status Codes

| Code | Usage                                      |
|------|--------------------------------------------|
| 200  | Successful GET, PUT/PATCH                  |
| 201  | Successful POST (resource created)         |
| 204  | Successful DELETE (no content)             |
| 400  | Bad request                                |
| 401  | Unauthenticated (missing or invalid token) |
| 403  | Unauthorized (wrong role)                  |
| 404  | Resource not found                         |
| 422  | Validation error or business rule violated |
| 500  | Server error                               |

## Authentication

All protected routes require the header:

```http
Authorization: Bearer {token}
```

## Pagination

Laravel default pagination. Use `per_page` parameter to control page size.

```json
{
  "success": true,
  "data": {
    "items": [],
    "meta": {
      "current_page": 1,
      "per_page": 15,
      "total": 100
    }
  }
}
```

## Validation Errors

Return 422 with field-level errors:

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "phone": ["The phone field is required."],
    "credit_limit": ["The credit limit must be a positive number."]
  }
}
```

## Resource Naming

| Resource     | Endpoint                              |
|--------------|---------------------------------------|
| Auth         | POST /api/v1/auth/login               |
| Auth         | POST /api/v1/auth/logout              |
| Users        | GET/POST /api/v1/users                |
| Users        | GET/PUT/DELETE /api/v1/users/{id}     |
| Categories   | GET/POST /api/v1/categories           |
| Categories   | GET/PUT/DELETE /api/v1/categories/{id}|
| Products     | GET/POST /api/v1/products             |
| Products     | GET/PUT/DELETE /api/v1/products/{id}  |
| Farmers      | GET/POST /api/v1/farmers              |
| Farmers      | GET/PUT/DELETE /api/v1/farmers/{id}   |
| Transactions | GET/POST /api/v1/transactions         |
| Transactions | GET /api/v1/transactions/{id}         |
| Debts        | GET /api/v1/farmers/{id}/debts        |
| Repayments   | POST /api/v1/repayments               |
