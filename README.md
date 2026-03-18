Here is the **complete single-file `README.md`** for **Bayyin v2.4.0**, fully formatted and ready to place in your project root or GitHub repository.

You can **copy everything below directly into `README.md`**.

---

````markdown
# Bayyin v2.4.0

Lightweight **PHP Native MVC Framework** designed for secure and rapid development of internal systems, dashboards, CRUD applications, and APIs.

Bayyin provides a **clean MVC structure, built-in security utilities, database migration tools, and a CLI scaffolding suite** to accelerate PHP application development without requiring heavy frameworks.

---

# Key Features

## Core Framework

- PHP Native MVC architecture
- Clean routing system
- Modular controller structure
- Reusable layouts and views
- AdminLTE-ready UI integration

## Security

- CSRF protection
- Role-based middleware
- Secure session management
- Request validation helpers
- Rate limiting
- Logging system
- Input filtering

## Database Layer

- PDO database abstraction
- Migration system
- Seeder system
- Transaction support
- Safe query execution

## Developer Tools

- CLI command tool (`bayyin.php`)
- Code scaffolding generators
- Migration generator
- Seeder generator
- Module scaffolding
- Route inspector
- Configuration inspector

## API Support

- REST API routing
- API response helpers
- JSON request support
- DataTables server-side support

---

# Requirements

- PHP **8.1+**
- MySQL / MariaDB
- Apache / Nginx
- XAMPP / Laragon / LAMP / WAMP
- Composer (optional but recommended)

---

# Installation

## 1. Clone or Download

Clone the repository:

```bash
git clone https://github.com/fauzibspp/bayyin.git
```
````

Or download the ZIP file and extract it.

---

## 2. Setup Environment

Copy `.env.example` to `.env`

```bash
cp .env.example .env
```

Configure your environment:

```
APP_NAME=Bayyin
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=sampledb
DB_USER=root
DB_PASS=

SESSION_NAME=secureapp_session
```

---

## 3. Create Database

Create database in MySQL:

```
sampledb
```

Or use the database name defined in `.env`.

---

## 4. Run Migration

Create the database tables:

```bash
php cli/bayyin.php migrate
```

---

## 5. Seed Default Data

Insert default data:

```bash
php cli/bayyin.php seed
```

---

## 6. Start Local Development Server

```bash
php cli/bayyin.php serve
```

Open browser:

```
http://localhost:8080
```

---

# Default Login

```
Email: admin@example.com
Password: password123
```

---

# CLI Commands

Bayyin provides a built-in CLI toolkit for development.

---

## Start Development Server

```
php cli/bayyin.php serve
```

---

## Database Migration

Run migrations

```
php cli/bayyin.php migrate
```

Check migration status

```
php cli/bayyin.php migrate:status
```

Rollback last migration

```
php cli/bayyin.php rollback
```

---

## Database Seeder

Seed database

```
php cli/bayyin.php seed
```

Generate new seeder

```
php cli/bayyin.php make:seeder ProductSeeder
```

---

## Code Generators

Generate controller

```
php cli/bayyin.php make:controller ProductController
```

Generate model

```
php cli/bayyin.php make:model ProductModel products
```

Generate middleware

```
php cli/bayyin.php make:middleware AuditMiddleware
```

Generate migration

```
php cli/bayyin.php make:migration create_products_table
```

---

## Module Scaffolding

Generate full module structure:

```
php cli/bayyin.php make:module Product products
```

### Generate full module (Web + API)

php cli/bayyin.php make:full-module Product products "name:string,price:decimal,stock:int,is_active:boolean,description:text"

This automatically creates:

- Controller
- Model
- Views folder
- Basic CRUD structure

---

## Development Tools

List application routes

```
php cli/bayyin.php route:list
```

Check application configuration

```
php cli/bayyin.php config:check
```

---

# Default Routes

## Web Routes

```
/
 /home
 /login
 /register
 /logout
 /users
 /users/create
 /users/edit
 /users/delete
```

## API Routes

```
/api/users
/api/users-datatable
```

---

# Project Structure

```
BayyinFramework/
│
├── app/
│   ├── Controllers
│   ├── Core
│   ├── Middleware
│   ├── Models
│   ├── Traits
│   └── Views
│
├── cli/
│   ├── bayyin.php
│   ├── make-controller.php
│   ├── make-model.php
│   ├── make-middleware.php
│   ├── make-module.php
│   ├── make-migration.php
│   ├── make-seeder.php
│   └── route-list.php
│
├── database/
│   └── migrations
│
├── public/
│   └── index.php
│
├── routes/
│   ├── web.php
│   └── api.php
│
├── storage/
│   ├── cache
│   ├── logs
│   └── sessions
│
└── .env
```

---

# Use Cases

Bayyin is suitable for:

- Admin dashboards
- CRUD systems
- School management systems
- HR systems
- Internal business systems
- REST API backends
- Reporting dashboards
- Small SaaS platforms

---

# Roadmap

## v2.5 (Next Release)

Planned improvements:

- generated AJAX create flow
- generated AJAX update flow
- generated AJAX delete flow refinement
- generated modal create/edit form2
- inline validation response format for API
- richer generated admin UX

---

# License

MIT License

---

# Author

Developed and maintained by

**fauzi**

Freelance Software Developer

Email:
[fauzi@sainet.my](mailto:fauzi@sainet.my)

```

```
