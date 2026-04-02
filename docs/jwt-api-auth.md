# jwt-api-auth
# JWT API Authentication
Bayyin uses session-based authentication for web routes and JWT authentication

## Environment settings
Set the following values in `.env`:
```
JWT_SECRET=change_this_to_a_long_random_secret_key
JWT_ALGO=HS256
JWT_TTL=3600
```md

## Login endpoint (request) POST:
Request
```
POST /api/auth/login
```md

http://localhost:8080/api/auth/login
Set in body via postman app
Body:
```
  {
  "email": "admin@example.com",
  "password": "password123"
  }
```md

Response
Json:
```
{
"success": true,
"message": "Login successful.",
"data": {
"token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzQ5MzA1ODQsIm5iZiI6MTc3NDkzMDU4NCwiZXhwIjoxNzc0OTM0MTg0LCJzdWIiOjEsImVtYWlsIjoiYWRtaW5AZXhhbXBsZS5jb20iLCJyb2xlIjoiYWRtaW4iLCJuYW1lIjoiQWRtaW5pc3RyYXRvciJ9.X5BIaZnNQzTneHod3ciYetkPa0UQWzp0mQY1XWZSwEY",
"token_type": "Bearer",
"expires_in": 3600,
"user": {
"id": 1,
"name": "Administrator",
"email": "admin@example.com",
"role": "admin"
}
},
"meta": []
}
```md

# Authenticated user endpoint
Request
```
  GET /api/auth/me
```md

http://localhost:8080/api/auth/me
Authorization -> Auth Type: Bearer Token
Copy and paste the token from the response result above
Press send button and the result like:
  {
  "success": true,
  "message": "Authenticated user fetched successfully.",
  "data": {
  "id": 1,
  "name": "Administrator",
  "email": "admin@example.com",
  "role": "admin",
  "iat": 1774930584,
  "exp": 1774934184
  },
  "meta": []
  }

# Generated API module protection
Generated API CRUD modules use JWT protection by default.

### Read endpoints
Usually require a valid token:
- GET /api/module
- GET /api/module/datatable
- GET /api/module/show
- GET /api/module/trash
- GET /api/module/export

### Write endpoints
Usually require admin role:
- POST /api/module/store
- POST /api/module/update
- POST /api/module/delete
- POST /api/module/bulk-delete
- POST /api/module/restore

# Common error responses
## Missing token
```
{
  "success": false,
  "message": "Unauthorized.",
  "errors": {
    "token": [
      "Bearer token is required."
    ]
  }
}
```md

## Invalid token
```
{
  "success": false,
  "message": "Invalid or expired token.",
  "errors": {
    "token": [
      "Token is invalid or expired."
    ]
  }
}
```md

## Forbidden role
```
{
  "success": false,
  "message": ".",Forbidden
  "errors": {
    "role": [
      "Insufficient permission."
    ]
  }
}
```md