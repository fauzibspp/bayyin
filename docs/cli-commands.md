# CLI Commands
## Core
### Serve development server
```bash
php cli/bayyin.php serve [host] [port]

Example:
php cli/bayyin.php serve localhost 8080 or php cli/bayyin.php serve (default is localhost:8080)
```

## Show framework version
```bash
php cli/bayyin.php version
```

## Show framwork summary
```bash
php cli/bayyin.php about
```

## Check current configuration
```bash
php cli/bayyin.php config:check
```

## Database
### Run migrations
```bash
php cli/bayyin.php migrate
```

### Roll back last migration batch
```bash
php cli/bayyin.php rollback
```

### Show migration status
```bash
php cli/bayyin.php migrate:status
```

### Run seeders
```bash
php cli/bayyin.php make:migration create_users_table
```

## Generators
### Make migration
```bash
php cli/bayyin.php make:migration create_users_table
```

### Make controller
```bash
php cli/bayyin.php make:controller ProductController
```

### Make model
```bash
php cli/bayyin.php make:model ProductModel products
```

### Make middleware
```bash
php cli/bayyin.php make:middleware AuditMiddleware
```

### Make simple module
```bash
php cli/bayyin.php make:module Product products
```

### Make seeder
```bash
php cli/bayyin.php make:seeder ProductSeedar
```

### Make simple module
```bash
php cli/bayyin.php make:module Product products
```

### Make seeder
```bash
php cli/bayyin.php make:seeder ProductSeeder
```

### Make request
```bash
php cli/bayyin.php make:request StoreProductRequest
```

### Make web CRUD
```bash
php cli/bayyin.php make:crud Product products "name:string,price:decimal,stock:int"
```

### Make API CRUD
```bash
php cli/bayyin.php make:api-crud Product products "name:string,price:decimal,stock:int"
```

### Make full module
```bash
php cli/bayyin.php make:full-module Product products "name:string,price:decimal,stock:int"
```

## Routing
### List all routes
```bash
php cli/bayyin.php route:list
```