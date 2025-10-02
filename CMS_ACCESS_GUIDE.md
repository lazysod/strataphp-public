# StrataPHP CMS - Quick Access Guide

## 🚀 Immediate Access

### **1. Admin Login**
Visit: **`/admin/admin_login.php`**
- Use your admin credentials to log in
- If you don't have admin credentials, create them using: `php bin/create_admin.php`

### **2. Main Admin Dashboard**
Visit: **`/admin/dashboard`**
- Access the main StrataPHP admin panel
- CMS is now integrated into the admin navigation menu

### **3. CMS Features**
Via Admin Menu or Direct Links:
- **CMS Dashboard**: `/admin/cms` 
- **Manage Pages**: `/admin/cms/pages`
- **Create Page**: `/admin/cms/pages/create`

### **4. Create Your First Page**
Visit: **`/admin/cms/pages/create`**
- Title: "About Us"
- Content: Add your content
- Status: "Published" 
- Save to create the page

### **5. View Your Pages**
- **Homepage**: `/` (displays page with slug 'home')
- **Custom Pages**: `/{slug}` (e.g., `/about-us`)
- **API Access**: `/api/cms/pages` (JSON format)

## 🎯 Default Content

The CMS comes with a default homepage:
- **Title**: "Welcome to StrataPHP"
- **URL**: `/` or `/home`
- **Status**: Published

You can edit this page through the admin interface.

## 🔧 Quick Setup Commands

```bash
# Ensure migrations are run
php bin/migrate.php

# Create admin user (if needed)
php bin/create_admin.php

# Check module status
# Verify 'cms' is enabled in /app/config.php
```

## 📞 Need Help?

Check the full documentation in `/modules/cms/README.md` for detailed features, API reference, and troubleshooting.

---
**Ready to start? Go to `/admin/dashboard` after logging in to access the CMS through the admin menu!** 🎉