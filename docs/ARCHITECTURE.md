# StrataPHP Architecture Overview

StrataPHP is a modular, PSR-native MVC framework for PHP 8.2+ designed for flexibility, explicitness, and extensibility. This document provides a high-level overview of the framework's architecture, focusing on its modular boot process, decentralized routing, and core design principles.

## Key Architectural Principles

- **Modularity:** All features are implemented as modules. Core functionality, CMS, admin, and API features are all modules that can be enabled, disabled, or replaced.
- **Explicitness:** There is no "magic" or hidden behavior. All configuration and routing is explicit and discoverable.
- **PSR Compliance:** Follows PSR-4 autoloading and other PHP-FIG standards for interoperability.

## Modular Boot Process

- On application startup, StrataPHP scans the `public_html/modules/` directory for enabled modules.
- Each module contains its own `index.php` metadata file and a `routes.php` file for route definitions.
- The framework loads and initializes each enabled module in order, registering routes, services, and event listeners as defined by the module.
- There is **no central `routes.php`** file. Instead, each module is responsible for its own routing and configuration.

## Routing

- Routes are defined in each module's `routes.php` file.
- The router aggregates all routes from enabled modules at boot time.
- This allows modules to be developed, tested, and deployed independently, with no risk of route conflicts in a central file.

## Directory Structure

- `public_html/modules/` — All modules live here. Each module is self-contained.
- `app/` — Application-wide configuration, helpers, and shared services.
- `storage/` — Logs, cache, and other runtime files.
- `vendor/` — Composer dependencies.

## Module Anatomy

A typical module contains:

- `index.php` — Module metadata (name, version, dependencies, etc.)
- `routes.php` — Route definitions for the module
- `controllers/`, `models/`, `views/`, `assets/` — MVC structure
- `migrations/`, `seeds/` — Database migrations and seeders

## Boot Sequence

1. Composer autoload is initialized.
2. Application config and environment are loaded.
3. All enabled modules are discovered and their `index.php` files are read.
4. Each module's `routes.php` is loaded and routes are registered.
5. The router dispatches the incoming request to the appropriate module/controller.

## Benefits

- **Loose Coupling:** Modules can be added, removed, or replaced without affecting the rest of the system.
- **Scalability:** Features can be developed and maintained independently.
- **Clarity:** No hidden or implicit behavior; everything is defined in module code.

## Example: Adding a Module

1. Place your module in `public_html/modules/YourModule/`.
2. Ensure it has an `index.php` and `routes.php`.
3. Enable the module in the admin or config.
4. The framework will automatically discover and register its routes and services at boot.

---

For more details, see the Module Development Guide and README.
