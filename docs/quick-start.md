# Quick Start

## Generate a full module
php cli/bayyin.php make:full-module Customer customers "name:string,email:string,phone:string,notes:text,is_active:boolean,deleted_at:datetime"

## Run migration
php cli/bayyin.php migrate

## Check routes
php cli/bayyin.php route:list

## Start server
php cli/bayyin.php serve

## Test web module
Open:
http://localhost:8080/customers

## Test API login
Endpoint:
POST /api/auth/login

Body JSON:
  {
  "email": "admin@example.com",
  "password": "password123"
  }

## Test protected API use
Use Bearer token from login response.
Example:
GET /api/customers
Authorization: Bearer <token>