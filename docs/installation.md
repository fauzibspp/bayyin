# Installation

## 1. Clone or copy project
Place Bayyin in your project directory.

## 2. Install Composer dependencies
```bash
composer install
```
Or if dependencies are already defined and vendor is missing:

```bash
composer dump-autoload
```

## 3. Create environment file
Copy `.env.example` to `.env`

## 4. Configure database and JWT settings
Set values for:
- DB_HOST
- DB_PORT
- DB_NAME
- DB_USER
- DB_PASS
- JWT_SECRET
- JWT_ALGO
- JWT_TTL

## 5. Run migrations
```bash
php cli/bayyin.php migrate
```

## 6. Start development server
```bash
php cli/bayyin.php serve
```

Default URL:
http://localhost:8080

## 7. Verify framework
Run:
```bash
php cli/bayyin.php version
php cli/bayyin.php about
php cli/bayyin.php help
php cli/bayyin.php config:check
php cli/bayyin.php route:list

```

## 8. Recommended repository root structure for v3.0.0
Make sure your project root now looks like this:

```text
app/
cli/
database/
docs/
public/
routes/
storage/
vendor/
.env.example
.gitignore
CHANGELOG.md
composer.json
LICENSE
README.md
VERSION.md

## 9. Suggested GitHub release title
```bash
Bayyin v3.0.0 - First Public Production Release
```

```bash
Bayyin v3.0.0 is the first public production release of the Bayyin PHP MVC framework.

Included:
- CLI scaffolding suite
- migrations and seeders
- schema-driven web CRUD and API CRUD generation
- JWT authentication for API
- DataTables-ready admin modules
- modal and AJAX CRUD flow
- soft delete lifecycle with trash/restore
- CSV export
- audit logging integration

This release establishes Bayyin as a reusable public framework baseline for internal systems, admin dashboards, and API-backed applications.
```

## 10. Recommended final v3.0.0 test checklist
```bash
composer dump-autoload
php cli/bayyin.php version
php cli/bayyin.php about
php cli/bayyin.php help
php cli/bayyin.php config:check
php cli/bayyin.php make:full-module Sample samples "name:string,status:string,notes:text,is_active:boolean,deleted_at:datetime"
php cli/bayyin.php route:list
php cli/bayyin.php migrate
php cli/bayyin.php serve
```
Then verify:
- /login
- /samples
- /samples/export
- /samples/trash
- /api/auth/login
- /api/samples
- /api/samples/datatable
- /api/samples/export
- /api/samples/trash