# Module Development Guide

StrataPHP makes module development fast and flexible. Follow these steps and best practices to create robust, reusable modules.

## Creating a Module
1. Use the CLI tool:
   ```sh
   php bin/create-module.php MyModule
   ```
   This generates a module skeleton with PSR-4 structure.

2. Add your controllers, models, views, and assets:
   - Place controllers in `controllers/`
   - Models in `models/`
   - Views/templates in `views/`
   - Static assets in `assets/`

3. Define module metadata in `index.php`:
   - Name, version, description, dependencies

## PSR-4 Autoloading
All classes should use namespaces matching their folder structure.
Example (old style):
```php
namespace App\Modules\MyModule\Controllers;
class ExampleController {}
```

If you have moved to the new StrataPHP\Modules namespace convention:
```php
namespace StrataPHP\Modules\MyModule\Controllers;
class ExampleController {}
```
Composer will autoload these classes automatically.

## Module Structure Example
```
MyModule/
  index.php
  README.md
  controllers/
  models/
  views/
  assets/
  migrations/
  seeds/
  config/
```

## Installing Modules
- Use the CLI tool:
  ```sh
  php bin/install-module.php MyModule
  ```
- Or copy the module folder to `public_html/modules/`.

## Validating Modules

Check if a module meets StrataPHP standards before enabling:

```sh
php bin/create-module.php --validate blog

### Installing from a Git Repository
- Clone the module repository directly into your modules folder:
  ```sh
  git clone https://github.com/username/module-repo.git public_html/modules/ModuleName
  ```
- Ensure the module follows StrataPHP’s structure and includes `index.php` metadata.
- If the module supports Composer, you can add it as a dependency in your composer.json:
  ```json
  "require": {
    "vendor/module-repo": "^1.0"
  }
  ```
  Then run:
  ```sh
  composer install
  ```

Refer to the module’s README for any additional setup steps.

## Best Practices
- Keep modules self-contained and documented
- Use migrations for database changes
- Follow PSR-4 and Composer standards
- Write unit tests in `tests/`
- Provide a clear README.md and CHANGELOG.md

## Troubleshooting
- Check module metadata for errors
- Use CLI tools for validation
- Review logs in `storage/logs/`

## Module Validation

StrataPHP provides tools and guidelines to help you validate your modules:

- **CLI Validation:**
  Use the CLI tool to validate a module:
  ```sh
  php bin/create-module.php --validate MyModule
  ```
  This checks for required metadata, structure, and common issues.

- **Manual Checks:**
  - Ensure `index.php` contains valid metadata (name, version, dependencies).
  - Confirm PSR-4 namespaces match the folder structure.
  - Check for missing or extra files in controllers, models, views, etc.
  - Run unit tests in the `tests/` directory if available.

Validation helps ensure modules are compatible, secure, and easy to maintain.

## Logging in Modules
StrataPHP provides a built-in Logger class for writing log messages to `storage/logs/app.log` (or the path set in your config). Use this for error, info, or warning messages in your modules.

**Example:**
```php
use App\Logger;

$logger = new Logger($config); // Pass the app config array
$logger->info('Module loaded', ['module' => 'MyModule']);
$logger->error('Something went wrong', ['exception' => $e->getMessage()]);
```

- Log files are stored in `storage/logs/` by default.
- Log levels: `info`, `warning`, `error`.
- Always log sensitive errors instead of displaying them to users.

For advanced topics, see the full framework documentation.
