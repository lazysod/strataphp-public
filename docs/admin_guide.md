# Admin Module & Module Manager Guide

## 1. Overview of the Admin Module
The Admin Module provides a web-based interface for managing your application. It includes user management, module management, and access to various system settings. Out of the box, it is designed to be extensible and secure.

### Key Features
- User authentication and roles
- Module management (install, enable, disable, remove)
- Access to core settings and logs
- Extensible via custom modules

## 2. Accessing the Admin Panel
- **Default URL:** `/admin/`
- **Login:** Use the credentials created during installation or via the `bin/create_admin.php` script.
- **User Roles:** Admin users have full access. Additional roles can be defined via modules or custom code.

## 3. Navigating the Admin Interface
- **Dashboard:** Overview of system status and quick links.
- **Users:** Manage admin users and permissions.
- **Modules:** View, install, enable/disable, and remove modules.
- **Settings:** Access core configuration options.

## 4. The Module Manager
The Module Manager is built into the admin panel and allows you to:
- **Install Modules:** Upload or select modules to add new features.
- **Enable/Disable Modules:** Toggle modules on or off without uninstalling.
- **Remove Modules:** Uninstall modules cleanly.
- **View Standard Modules:** See which modules are included by default.

### Standard Modules
- User management
- Content management (CMS)
- File uploads
- Logging and monitoring

## 5. Extending the Admin Module
- **Create Custom Modules:** Use the `bin/create-module.php` script to scaffold a new module.
- **Integration:** Place your module in the `modules/` directory. It will appear in the Module Manager.
- **Best Practices:** Follow the structure of existing modules for compatibility and maintainability.

## 6. Troubleshooting & FAQs
- **Locked Out?** Use `bin/create_admin.php` to create a new admin user.
- **Module Not Appearing?** Ensure it is in the correct directory and follows the required structure.
- **Need More Help?** See the main README or contact support.

---

This guide provides a starting point for working with the admin module and module manager. For advanced customization, refer to the developer documentation or explore the codebase.