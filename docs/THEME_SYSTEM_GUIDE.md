# StrataPHP Theme System - Complete Guide

## 🎨 **Dual Theme Architecture**

StrataPHP uses a **dual theme system** to provide maximum flexibility:

### **1. Framework Themes** (Admin/System Pages)
- **Location**: `htdocs/themes/`
- **Purpose**: Admin panels, login pages, system interfaces
- **Configuration**: `theme.json` files
- **Scope**: Framework-level pages and modules

### **2. CMS Themes** (Content Pages)
- **Location**: `htdocs/themes/cms/`
- **Purpose**: Public content pages managed by CMS
- **Configuration**: `htdocs/modules/cms/config/theme.php`
- **Scope**: CMS-generated public content

---

## 🏗️ **Framework Theme System**

### **Theme Structure**
```
htdocs/themes/
├── default/                    ← Default framework theme
│   ├── theme.json             ← Theme configuration
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── views/                 ← Theme-specific view overrides
├── admin/                     ← Admin-specific theme
└── custom/                    ← Your custom themes
```

### **Framework Theme Configuration** (`theme.json`)
```json
{
  "name": "Default Theme",
  "author": "Strata Team", 
  "version": "1.0",
  "description": "Default framework theme",
  "logo": "/assets/images/logo_small.png",
  "favicon": "/assets/images/favicon.ico",
  "css": "/css/styles.css",
  "js": "/js/scripts.js",
  "bootstrap_version": "5.3",
  "supports": ["admin", "auth", "modules"]
}
```

### **Setting Framework Theme**
In `htdocs/app/config.php`:
```php
'theme' => 'default',           // Framework theme name
'theme_path' => '/themes/default',
'theme_config' => [
    'name' => 'Default Theme',
    'author' => 'Strata Team',
    'version' => '1.0',
    // ... theme configuration
],
```

---

## 🎯 **CMS Theme System**

### **CMS Theme Structure**
```
htdocs/themes/cms/
├── modern/                    ← Modern CMS theme
│   ├── templates/
│   │   ├── default.php       ← Default page template
│   │   ├── full-width.php    ← Full-width template
│   │   └── sidebar.php       ← Sidebar template
│   └── assets/
│       ├── css/
│       ├── js/
│       └── images/
├── minimal/                   ← Minimal CMS theme
└── blog/                      ← Blog-focused theme
```

### **CMS Theme Configuration** 
In `htdocs/modules/cms/config/theme.php`:
```php
return [
    'default_theme' => 'modern',
    'themes' => [
        'modern' => [
            'name' => 'Modern',
            'description' => 'Clean, modern design',
            'author' => 'StrataPHP',
            'version' => '1.0.0',
            'templates' => ['default', 'full-width', 'sidebar'],
            'styles' => [
                'primary_color' => '#3498db',
                'secondary_color' => '#2c3e50',
                'accent_color' => '#e74c3c',
                'font_family' => 'Arial, sans-serif'
            ]
        ]
    ]
];
```

---

## 🔧 **Customizing Themes**

### **Creating a Framework Theme**

1. **Create theme directory**:
   ```
   htdocs/themes/mytheme/
   ```

2. **Add theme.json**:
   ```json
   {
     "name": "My Custom Theme",
     "author": "Your Name",
     "version": "1.0.0",
     "description": "My custom framework theme",
     "logo": "/assets/images/my-logo.png",
     "favicon": "/assets/images/favicon.ico",
     "css": "/css/custom.css",
     "js": "/js/custom.js"
   }
   ```

3. **Update config.php**:
   ```php
   'theme' => 'mytheme',
   'theme_path' => '/themes/mytheme',
   ```

### **Creating a CMS Theme**

1. **Create CMS theme directory**:
   ```
   htdocs/themes/cms/mytheme/
   ├── templates/
   │   ├── default.php
   │   └── full-width.php
   └── assets/
       ├── css/style.css
       └── js/script.js
   ```

2. **Add to CMS theme config**:
   ```php
   // In htdocs/modules/cms/config/theme.php
   'themes' => [
       'mytheme' => [
           'name' => 'My Theme',
           'description' => 'Custom CMS theme',
           'author' => 'Your Name',
           'version' => '1.0.0',
           'templates' => ['default', 'full-width'],
           'styles' => [
               'primary_color' => '#your-color',
               // ... your styles
           ]
       ]
   ];
   ```

3. **Set as default** (optional):
   ```php
   'default_theme' => 'mytheme',
   ```

---

## 🎨 **Template System**

### **Framework Templates**
Used for admin panels, login pages, module interfaces:
- Located in `htdocs/views/` or `htdocs/themes/{theme}/views/`
- PHP-based templates with optional Twig support
- Shared across all framework modules

### **CMS Templates**
Used for public content pages:
- Located in `htdocs/themes/cms/{theme}/templates/`
- Specialized for content presentation
- Support multiple layouts per theme

### **Template Variables**
Available in CMS templates:
```php
<?php
// Page data
echo $page['title'];        // Page title
echo $page['content'];      // Page content  
echo $page['meta_title'];   // SEO title
echo $page['meta_description']; // SEO description

// Theme data
echo $theme['name'];        // Theme name
echo $theme['styles']['primary_color']; // Theme colors
?>
```

---

## 🚀 **Best Practices**

### **Framework Themes**
- ✅ Use semantic HTML and Bootstrap classes
- ✅ Include responsive design
- ✅ Follow StrataPHP naming conventions
- ✅ Test with admin panel and auth pages
- ✅ Support both light and dark modes (optional)

### **CMS Themes**
- ✅ Create multiple template options
- ✅ Use semantic markup for SEO
- ✅ Include print styles
- ✅ Optimize for page loading speed
- ✅ Support social media metadata

### **Asset Management**
- ✅ Minimize CSS/JS files for production
- ✅ Use CDNs for common libraries
- ✅ Include version numbers for cache busting
- ✅ Optimize images for web

---

## 📁 **Theme File Structure Examples**

### **Complete Framework Theme**
```
mytheme/
├── theme.json              ← Theme configuration
├── assets/
│   ├── css/
│   │   ├── framework.css   ← Framework-specific styles
│   │   ├── admin.css       ← Admin panel styles
│   │   └── auth.css        ← Authentication styles
│   ├── js/
│   │   ├── framework.js    ← Framework interactions
│   │   └── admin.js        ← Admin functionality
│   └── images/
│       ├── logo.png
│       └── favicon.ico
└── views/                  ← Template overrides (optional)
    ├── admin/
    └── auth/
```

### **Complete CMS Theme**
```
mytheme/
├── templates/
│   ├── default.php         ← Standard page layout
│   ├── full-width.php      ← Full-width layout
│   ├── sidebar.php         ← Sidebar layout
│   └── landing.php         ← Landing page layout
├── assets/
│   ├── css/
│   │   ├── theme.css       ← Main theme styles
│   │   ├── print.css       ← Print styles
│   │   └── mobile.css      ← Mobile-specific styles
│   ├── js/
│   │   ├── theme.js        ← Theme interactions
│   │   └── components.js   ← UI components
│   └── images/
│       ├── backgrounds/
│       ├── icons/
│       └── placeholders/
└── partials/               ← Reusable components
    ├── header.php
    ├── footer.php
    └── navigation.php
```

---

## 🔄 **Theme Switching**

### **CMS Toggle Integration**
When CMS is disabled, the system automatically:
- ✅ Falls back to framework themes for all pages
- ✅ Maintains admin functionality with framework themes
- ✅ Preserves user experience without CMS-specific styling

### **Dynamic Theme Selection**
```php
// Framework theme switching
App::setTheme('mytheme');

// CMS theme switching (via ThemeManager)
$themeManager = new \App\Modules\Cms\ThemeManager();
$themeManager->setTheme('modern');
```

---

## 🛠️ **Troubleshooting**

### **Common Issues**

**Theme not loading:**
- ✅ Check `theme.json` syntax
- ✅ Verify file permissions
- ✅ Confirm asset paths are correct

**CMS theme not applying:**
- ✅ Verify CMS module is enabled
- ✅ Check theme configuration in `cms/config/theme.php`
- ✅ Clear any theme caches

**Assets not loading:**
- ✅ Check relative vs absolute paths
- ✅ Verify web server configuration
- ✅ Test asset URLs directly

### **Development Tips**
- 🔧 Use browser dev tools to debug CSS
- 🔧 Test themes in different screen sizes
- 🔧 Validate HTML markup
- 🔧 Check for console errors

---

**The StrataPHP dual theme system provides maximum flexibility for both framework functionality and content presentation!** 🎨