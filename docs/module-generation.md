# Module Generation
Bayyin supports three main generator flows.

## 1. Web CRUD generation
```bash
php cli/bayyin.php make:crud Product products "name:string,price:decimal,stock:int"
```

This generates:
- controller
- module
- request
- seeder
- views
- migration
- web routes

## 2. API CRUD generation
```bash
php cli/bayyin.php make:api-crud Product products "name:string,price:decimal,stock:int"
```

This generates:
- API controller
- JWT-protected API routes
- datatable endpoint
- bulk delete endpoint
- export CSV endpoint
- trash and restore endpoints soft delete exists

## 3. Full module generation
```bash
php cli/bayyin.php make:full-module Product products "name:string,price:decimal,stock:int,is_active:boolean,description:text"
```
This combines both web and API generation.

Soft delete behavior
if the schema includes:
```bash
deleted_at:datetime
```

# Web
- /module/trash
- /module/restore
- /module/export

# API
- /api/module/trash
- /api/module/restore
- /api/module/export
Delete actions become soft-delete aware.

# DataTables-ready admin modules
## Generated web modules include:
- DataTables-ready listing
- modal create and edit forms
- AJAX create, update, delete
- bulk delete

## Audit loggin integration
Generated modules include audit loggin calls in:
- create
- update
- delete
- restore
- bulk delete items

## Example
```bash
php cli/bayyin.php make:full-module Customer customers "name:string,email:string,phone:string,notes:text,is_active:boolean,deleted_at:datetime"
php cli/bayyin.php migrate
```
## Then test:
Web
- /customers
- /customers/trash
- /customers/export
API
- /api/customers
- /api/customers/datatable
- /api/customers/export
- /api/customers/trash
- /api/customers/restore