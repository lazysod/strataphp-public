# StrataPHP Framework - Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.0] - 2025-10-09

### 🎉 Enhanced Migration System (October 2025)

#### Added
- **Migration Testing Script** (`bin/test_migrations.php`) - Comprehensive validation of all migrations
  - Format validation for all migration files
  - Rollback capability testing for every migration
  - Duplicate migration number detection
  - Database structure verification
- **Enhanced Rollback Script** (`bin/rollback.php`) - Complete rewrite with advanced features
  - Dual format support (array format + separate .down.php files)
  - Automatic format detection
  - Multi-step rollback capability (e.g., `php bin/rollback.php 3`)
  - Robust error handling for missing or invalid rollbacks
- **Centralized Version Management** (`htdocs/app/Version.php`) - Professional version handling
  - Reads version from `composer.json` as canonical source
  - Removes version from user-editable config files
  - Provides version comparison and semantic parsing utilities
  - Automatic fallbacks when composer.json unavailable
- **Complete Migration Documentation** (`docs/MIGRATION_SYSTEM_GUIDE.md`)
  - Comprehensive usage guide
  - Migration format examples
  - Best practices and safety guidelines
  - Testing procedures

#### Enhanced
- **Migration Coverage** - All migrations now have complete rollback support
  - Created missing `.down.php` files for legacy migrations
  - Standardized array format where beneficial
  - Ensured idempotent operations across all migrations
- **Migration Safety** - Bulletproof reliability for production use
  - Improved error handling and validation
  - Better user feedback during migration operations
  - Consistent patterns across all migration files

#### Fixed
- Missing rollback functionality for several legacy migrations
- Inconsistent migration patterns across different files
- Lack of comprehensive testing for migration system

### 🚀 CMS Toggle System (October 2025)

#### Added
- **CMS Module Toggle** - Safe enable/disable functionality for CMS module
  - Zero breaking changes when switching modes
  - Graceful degradation maintaining full site functionality
  - Professional fallback system with automatic theme switching
- **Adaptive Theming System** - Smart theme detection and fallbacks
  - Automatic fallbacks to default StrataPHP themes when CMS disabled
  - Context-aware redirects based on user role and module availability
  - Seamless user experience regardless of CMS state

See `CHANGELOG_CMS_TOGGLE.md` for detailed CMS toggle implementation details.

## [1.0.0] - Initial Release

### Added
- Core StrataPHP framework with MVC architecture
- Module system for extensible functionality
- Database migration and seeding system
- User authentication and session management
- Twig template engine support (optional)
- Admin panel with secure authentication
- RESTful API foundation
- Contact management module
- Links management module
- Home page module
- Multiple theme support

### Framework Features
- **Routing** - Clean URL routing with parameter support
- **Controllers** - MVC pattern implementation
- **Views** - Both PHP and Twig template support
- **Database** - PDO-based database abstraction
- **Security** - CSRF protection, secure sessions, password hashing
- **Modules** - Plugin-like architecture for feature extensibility
- **Config** - Environment-based configuration management
- **Logging** - Comprehensive application logging

---

## Migration Guide

When upgrading between versions:

1. **Always backup your database** before running migrations
2. **Test migrations** in a development environment first
3. **Run the migration test script**: `php bin/test_migrations.php`
4. **Apply migrations**: `php bin/migrate.php`
5. **Verify the upgrade** by testing key functionality

For detailed migration information, see `docs/MIGRATION_SYSTEM_GUIDE.md`.

## Support

- **Documentation**: See `README.md` and files in the `docs/` directory
- **Migration Help**: `docs/MIGRATION_SYSTEM_GUIDE.md`
- **Module Development**: `MODULE_SYSTEM.md` and `MODULE_STANDARDS.md`
- **CMS Toggle**: `CHANGELOG_CMS_TOGGLE.md`

---

**StrataPHP Framework** - Professional PHP development made simple. 🚀