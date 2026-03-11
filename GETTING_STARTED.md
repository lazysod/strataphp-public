# Getting Started with StrataPHP

Welcome to StrataPHP—a modular PHP framework designed for rapid development, clear structure, and easy module integration.

## Prerequisites
- PHP 8.0+ (with PDO, mbstring, and openssl extensions)
- Composer
- MySQL or MariaDB

## Installation Steps
1. **Clone the repository:**
   ```sh
   git clone https://github.com/lazysod/strataphp-dev.git
   cd strataphp-dev
   ```
2. **Install Composer dependencies:**
   ```sh
   composer install
   ```
3. **Configure environment:**
   - Copy `.env.example` to `.env`:
     ```sh
     cp .env.example .env
     ```
   - Edit `.env` with your database, mail, and other settings.
4. **Set up your web server:**
   - Point your document root to `public_html/`.
5. **Import the database schema:**
   ```sh
   php bin/install.php
   ```
6. **Run migrations:**
   ```sh
   php bin/migrate.php
   ```
7. **Create an admin account:**
   ```sh
   php bin/create_admin.php
   ```
8. **Access the site:**
   - Visit your domain in a browser.

## PSR-4 Autoloading
StrataPHP uses PSR-4 autoloading for all core and module classes. Your modules should follow this structure:

```
module-name/
  controllers/
  models/
  views/
  index.php
```

Composer automatically loads classes from these folders. See the Module Development guide for details.

## Next Steps
- Explore the admin panel at `/admin/dashboard`
- Review core modules and their features
- Start developing your own modules

For troubleshooting and advanced setup, see the full documentation.
