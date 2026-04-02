# Installation

## 1. Clone or copy project
Place Bayyin in your project directory.

## 2. Install Composer dependencies
composer install

Or if dependencies are already defined and vendor is missing:

composer dump-autoload

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
php cli/bayyin.php help
php cli/bayyin.php route:list
php cli/bayyin.php config:check
```