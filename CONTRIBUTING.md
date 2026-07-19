# CONTRIBUTING

## API Conventions

### Base URL

```
/api
```

---

## Resource Naming

Use plural nouns.

Examples:

```
/users
/customers
/leads
/tasks
```

---

## HTTP Methods

| Method | Purpose |
|---------|---------|
| GET | Retrieve resources |
| POST | Create a resource |
| PUT | Replace a resource |
| PATCH | Update a resource |
| DELETE | Delete a resource |

---

## Success Responses

### Single Resource

```json
{
    "data": {
        "id": 1,
        "name": "John Doe"
    },
    "meta": {
        "success": true
    }
}
```

### Collection

```json
{
    "data": [
        {
            "id": 1,
            "name": "John Doe"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 10,
        "total": 1
    }
}
```

---

## Error Responses

### Validation Error (422)

```json
{
    "message": "Validation failed.",
    "errors": {
        "email": [
            "The email field is required."
        ]
    }
}
```

### Unauthorized (401)

```json
{
    "message": "Unauthenticated."
}
```

### Forbidden (403)

```json
{
    "message": "This action is unauthorized."
}
```

### Not Found (404)

```json
{
    "message": "Resource not found."
}
```

---

## Pagination

Use:

```
?page=1&per_page=10
```

---

## Security Rules

Never expose:

- password
- remember_token
- deleted_at
- tokens
- internal IDs not required by the frontend

Always return API Resources instead of raw Eloquent models.

---

## Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK |
| 201 | Created |
| 204 | No Content |
| 401 | Unauthenticated |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Failed |
| 500 | Internal Server Error |
