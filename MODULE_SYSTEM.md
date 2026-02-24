# StrataPHP Module System Documentation

**Version:** 1.0.0  
**Date:** October 1, 2025  
**Framework:** StrataPHP  

## Table of Contents

1. [Overview](#overview)
2. [Module Architecture](#module-architecture)
3. [Creating Modules](#creating-modules)
4. [Installing Modules](#installing-modules)
5. [Module Structure](#module-structure)
6. [CLI Tools](#cli-tools)
7. [Admin Interface](#admin-interface)
8. [API Reference](#api-reference)
9. [Best Practices](#best-practices)
10. [Examples](#examples)
11. [Troubleshooting](#troubleshooting)

---

## Overview

StrataPHP features a powerful, modular architecture that allows developers to create, distribute, and install modules with unprecedented ease. The module system provides:

- **Automatic discovery** of modules in the filesystem
- **CLI tools** for generation and installation
- **Admin interface** for management
- **PSR-4 autoloading** integration
- **Composer compatibility**
- **Multiple installation sources** (GitHub, ZIP, local)

### Key Features

✅ **30-second module generation** with full MVC structure  
✅ **One-command installation** from multiple sources  
✅ **Automatic validation** and dependency checking  
✅ **Professional code structure** following PSR standards  
✅ **Bootstrap-ready views** with responsive design  
✅ **RESTful API endpoints** included by default  
✅ **Database integration** with migration support  
✅ **Admin panel integration** for easy management  

---

## Module Architecture

### Core Components

```
StrataPHP Framework
├── htdocs/
│   ├── modules/               # Module directory
│   │   ├── [module-name]/     # Individual modules
│   │   │   ├── index.php      # Module metadata
│   │   │   ├── routes.php     # Route definitions
│   │   │   ├── controllers/   # PSR-4 controllers
│   │   │   ├── models/        # PSR-4 models
│   │   │   ├── views/         # Template files
│   │   │   └── assets/        # CSS/JS/Images
│   │   └── ...
│   └── app/
│       ├── config.php         # Module configuration
│       └── start.php          # Module loader
├── bin/
│   ├── create-module.php      # Module generator
│   └── install-module.php     # Module installer
└── composer.json              # PSR-4 autoloading
```

### Module Loading Process

1. **Discovery:** Framework scans `/htdocs/modules/` directory
2. **Configuration:** Checks `config['modules']` for enabled status
3. **Autoloading:** Registers PSR-4 namespaces via Composer
4. **Route Loading:** Includes `routes.php` for enabled modules
5. **Admin Integration:** Shows in module manager interface

---

## Creating Modules

### Quick Start

Create a new module in 30 seconds:

```bash
php bin/create-module.php blog
```

This generates a complete module with:
- Full CRUD operations
- RESTful routes
- Database model
- Bootstrap views
- API endpoints
- Documentation

### Generated Structure

```
htdocs/modules/blog/
├── index.php                  # Module metadata
├── routes.php                 # Route definitions
├── controllers/
│   └── BlogController.php     # Main controller
├── models/
│   └── Blog.php              # Database model
├── views/
│   ├── index.php             # List view
│   ├── create.php            # Create form
│   ├── edit.php              # Edit form
│   └── show.php              # Detail view
├── assets/
│   ├── css/                  # Stylesheets
│   └── js/                   # JavaScript
└── README.md                 # Documentation
```

### Module Metadata (`index.php`)

```php
<?php
return [
    'name' => 'Blog',
    'slug' => 'blog',
    'version' => '1.0.0',
    'description' => 'A blog module for StrataPHP.',
    'author' => 'Your Name',
    'category' => 'Content',
    'enabled' => false,
    'suitable_as_default' => false,
    'dependencies' => ['user'],
    'permissions' => ['create_posts', 'manage_posts'],
    'update_url' => 'https://github.com/user/strataphp-blog'
];
```

---

## Installing Modules

### Installation Sources

The module installer supports multiple sources:

#### 1. GitHub Repositories
```bash
php bin/install-module.php https://github.com/user/strataphp-blog.git
```

#### 2. ZIP Files
```bash
php bin/install-module.php https://example.com/module.zip
```

#### 3. Local Directories
```bash
php bin/install-module.php /path/to/local/module
```

### Installation Process

1. **Source Validation:** Verifies the source is accessible
2. **Module Validation:** Checks required files and structure
3. **Conflict Detection:** Ensures module doesn't already exist
4. **File Copying:** Installs module to correct location
5. **Autoload Update:** Updates `composer.json` and runs `dump-autoload`
6. **Configuration:** Adds module to config (disabled by default)
7. **Install Script:** Runs optional `install.php` if present

### Installation Output

```
🚀 StrataPHP Module Installer
Source: https://github.com/user/strataphp-blog.git

📥 Cloning from Git...
🔍 Validating module structure...
✅ Module validation passed

📋 Module: Blog System v1.2.0
📋 Author: John Developer
📋 Description: A full-featured blog system

📂 Installing to: /htdocs/modules/blog
🔄 Updating Composer autoload...
📝 Added to config (disabled by default)

✅ Module 'blog' installed successfully!
🔧 Visit /admin/modules to enable the module
```

---

## Module Structure

### Required Files

| File | Purpose | Required |
|------|---------|----------|
| `index.php` | Module metadata | ✅ Yes |
| `routes.php` | Route definitions | ⚠️ Recommended |
| `controllers/` | MVC controllers | ⚠️ Recommended |
| `README.md` | Documentation | ⚠️ Recommended |

### Optional Files

| File | Purpose |
|------|---------|
| `models/` | Database models |
| `views/` | Template files |
| `assets/` | CSS/JS/Images |
| `install.php` | Installation script |
| `migrations/` | Database migrations |
| `config/` | Module configuration |

### Naming Conventions

- **Module Directory:** lowercase, hyphenated (`user-management`)
- **Classes:** PascalCase (`UserManagementController`)
- **Namespaces:** `App\Modules\[ModuleName]\Controllers`
- **Routes:** RESTful patterns (`/blog`, `/blog/create`, `/blog/{id}`)

---

## CLI Tools

### Module Generator

**Command:** `php bin/create-module.php <module-name>`

**Features:**
- Generates complete MVC structure
- Creates RESTful routes
- Includes API endpoints
- Adds PSR-4 autoloading
- Creates documentation

**Example:**
```bash
php bin/create-module.php ecommerce
```

### Module Installer

**Command:** `php bin/install-module.php <source>`

**Features:**
- Multiple source support
- Validation and conflict detection
- Automatic Composer integration
- Configuration management
- Error handling and rollback

**Examples:**
```bash
# From GitHub
php bin/install-module.php https://github.com/strataphp/forum.git

# From ZIP
php bin/install-module.php https://releases.com/module.zip

# From local directory
php bin/install-module.php ./my-custom-module
```

---

## Admin Interface

### Module Manager

**Location:** `/admin/modules`

**Features:**
- Visual module listing
- Enable/disable toggles
- Default module selection
- Module information display
- Bulk operations

### Configuration Management

Modules are configured in `/htdocs/app/config.php`:

```php
'modules' => [
    'blog' => [
        'enabled' => true,
        'suitable_as_default' => true
    ],
    'forum' => [
        'enabled' => false,
        'suitable_as_default' => false
    ]
]
```

---

## API Reference

### Module Controller Template

```php
<?php
namespace App\Modules\Blog\Controllers;

use App\DB;
use App\Modules\Blog\Models\Blog;

class BlogController
{
    private $db;
    private $config;
    
    public function __construct()
    {
        $this->config = include dirname(__DIR__, 3) . '/app/config.php';
        $this->db = new DB($this->config['db']);
    }
    
    // CRUD methods...
    public function index() { /* List items */ }
    public function create() { /* Show create form */ }
    public function store() { /* Save new item */ }
    public function show($id) { /* Show single item */ }
    public function edit($id) { /* Show edit form */ }
    public function update($id) { /* Update item */ }
    public function delete($id) { /* Delete item */ }
    
    // API endpoint
    public function apiIndex() { /* JSON API */ }
}
```

### Module Model Template

```php
<?php
namespace App\Modules\Blog\Models;

use App\DB;

class Blog
{
    private $db;
    private $table = 'blog_posts';
    
    public function __construct(DB $db)
    {
        $this->db = $db;
    }
    
    public function getAll() { /* Get all records */ }
    public function getById($id) { /* Get single record */ }
    public function create($data) { /* Create record */ }
    public function update($id, $data) { /* Update record */ }
    public function delete($id) { /* Delete record */ }
    public function search($query) { /* Search records */ }
    public function paginate($page, $perPage) { /* Paginated results */ }
}
```

### Routes Template

```php
<?php
use App\App;
use App\Modules\Blog\Controllers\BlogController;

global $router;

if (!empty(App::config('modules')['blog']['enabled'])) {
    // Main routes
    $router->get('/blog', [BlogController::class, 'index']);
    $router->get('/blog/create', [BlogController::class, 'create']);
    $router->post('/blog/create', [BlogController::class, 'store']);
    $router->get('/blog/{id}', [BlogController::class, 'show']);
    $router->get('/blog/{id}/edit', [BlogController::class, 'edit']);
    $router->post('/blog/{id}/edit', [BlogController::class, 'update']);
    $router->post('/blog/{id}/delete', [BlogController::class, 'delete']);
    
    // API routes
    $router->get('/api/blog', [BlogController::class, 'apiIndex']);
    
    // Default module support
    if (App::config('default_module') === 'blog') {
        $router->get('/', [BlogController::class, 'index']);
    }
}
```

---

## Best Practices

### Development Guidelines

1. **Follow PSR Standards**
   - PSR-4 autoloading
   - PSR-1 & PSR-12 coding standards
   - Proper namespacing

2. **Security First**
   - Validate all input
   - Use CSRF protection
   - Sanitize output
   - Check permissions

3. **Database Best Practices**
   - Use prepared statements
   - Implement proper indexing
   - Include migration scripts
   - Handle errors gracefully

4. **Performance Optimization**
   - Lazy load resources
   - Cache when appropriate
   - Optimize database queries
   - Minimize asset sizes

### Module Distribution

1. **Version Control**
   - Use semantic versioning
   - Tag releases properly
   - Maintain changelog
   - Document breaking changes

2. **Documentation**
   - Include comprehensive README
   - Document API endpoints
   - Provide usage examples
   - List dependencies

3. **Testing**
   - Include unit tests
   - Test installation process
   - Verify compatibility
   - Test uninstallation

---

## Examples

### Blog Module

A complete blog system with posts, categories, and comments.

**Features:**
- Post management (CRUD)
- Category organization
- Comment system
- SEO optimization
- RSS feeds

**Installation:**
```bash
php bin/install-module.php https://github.com/strataphp/blog-module.git
```

### E-commerce Module

A full e-commerce solution with products, cart, and checkout.

**Features:**
- Product catalog
- Shopping cart
- Order management
- Payment integration
- Inventory tracking

**Installation:**
```bash
php bin/install-module.php https://github.com/strataphp/ecommerce-module.git
```

### Forum Module

A discussion forum with threads, posts, and moderation.

**Features:**
- Forum categories
- Thread management
- User posts
- Moderation tools
- Search functionality

**Installation:**
```bash
php bin/install-module.php https://github.com/strataphp/forum-module.git
```

---

## Troubleshooting

### Common Issues

#### Module Not Found After Installation

**Problem:** Module installed but not appearing in admin panel.

**Solutions:**
1. Check if module is in `/htdocs/modules/` directory
2. Verify `index.php` exists with proper metadata
3. Run `composer dump-autoload`
4. Clear any caches

#### Routes Not Working

**Problem:** Module routes return 404 errors.

**Solutions:**
1. Ensure module is enabled in `/admin/modules`
2. Check `routes.php` syntax
3. Verify controller namespace and class names
4. Confirm route patterns are correct

#### Autoloading Issues

**Problem:** Class not found errors.

**Solutions:**
1. Check PSR-4 namespace in `composer.json`
2. Run `composer dump-autoload`
3. Verify file and class naming conventions
4. Check directory structure

#### Installation Failures

**Problem:** Module installation fails.

**Solutions:**
1. Check source URL accessibility
2. Verify write permissions on modules directory
3. Ensure Git is installed (for Git sources)
4. Check module structure validity

### Debug Mode

Enable debug mode in `/htdocs/app/config.php`:

```php
'debug' => true,
```

This provides detailed error messages and logging.

### Support

For additional support:

1. Check the [GitHub Issues](https://github.com/lazysod/strataphp-dev/issues)
2. Review module examples in the repository
3. Consult the framework documentation
4. Join the community discussions

---

## Conclusion

The StrataPHP module system provides a robust, developer-friendly platform for creating modular applications. With automatic generation tools, flexible installation options, and comprehensive admin interfaces, it enables rapid development and easy distribution of functionality.

The system is designed to be:
- **Simple** for beginners to use
- **Powerful** for advanced developers
- **Extensible** for complex applications
- **Maintainable** for long-term projects

Whether you're building a simple blog or a complex enterprise application, the StrataPHP module system provides the foundation for scalable, modular development.

---

**Last Updated:** October 1, 2025  
**Version:** 1.0.0  
**License:** Same as StrataPHP Framework