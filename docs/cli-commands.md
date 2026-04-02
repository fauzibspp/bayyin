# CLI Commands
## Core
### Serve development server
``` 
php cli/bayyin.php serve [host] [port]

Example:
php cli/bayyin.php serve localhost 8080 or php cli/bayyin.php serve (default is localhost:8080)

## Show framework version
```
php cli/bayyin.php version
```md

## Show framwork summary
```
php cli/bayyin.php about
```md

## Check current configuration
```
php cli/bayyin.php config:check
```md

## Database
### Run migrations
```
php cli/bayyin.php migrate
```md

### Roll back last migration batch
```
php cli/bayyin.php rollback
```md

### Show migration status
```
php cli/bayyin.php migrate:status
```md

### Run seeders
```
php cli/bayyin.php make:migration create_users_table
```md

## Generators
### Make migration
```
php cli/bayyin.php make:migration create_users_table
```md

### Make controller
```
php cli/bayyin.php make:controller ProductController
```md

### Make model
```
php cli/bayyin.php make:model ProductModel products
```md

### Make middleware
```
php cli/bayyin.php make:middleware AuditMiddleware
```md

### Make simple module
```
php cli/bayyin.php make:module Product products
```md

### Make seeder
```
php cli/bayyin.php make:seeder ProductSeedar
```md

### Make simple module
```
php cli/bayyin.php make:module Product products
```md

### Make seeder
```
php cli/bayyin.php make:seeder ProductSeeder
```md

### Make request
```
php cli/bayyin.php make:request StoreProductRequest
```md

### Make web CRUD
```
php cli/bayyin.php make:crud Product products "name:string,price:decimal,stock:int"
```md

### Make API CRUD
```
php cli/bayyin.php make:api-crud Product products "name:string,price:decimal,stock:int"
```md

### Make full module
```
php cli/bayyin.php make:full-module Product products "name:string,price:decimal,stock:int"
```md

## Routing
### List all routes
```
php cli/bayyin.php route:list
```md