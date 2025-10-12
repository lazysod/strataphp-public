# Template Customization Quick Start

## 🎯 **Quick Template Locations**

### **Where to Update Your Templates**

**Admin/Login Pages (Framework):**
```
htdocs/themes/default/     ← Framework theme
├── assets/css/styles.css  ← Update admin styling here
└── theme.json            ← Framework theme config
```

**Public Content Pages (CMS):**
```
htdocs/themes/cms/modern/  ← CMS theme
├── templates/            ← Update page layouts here
│   ├── default.php      ← Standard page template
│   ├── full-width.php   ← Full-width layout
│   └── sidebar.php      ← Sidebar layout
└── assets/css/          ← CMS styling here
```

---

## 🚀 **Common Customizations**

### **1. Change Site Colors**

**Framework Colors** (`htdocs/themes/default/assets/css/styles.css`):
```css
:root {
  --primary-color: #your-color;
  --secondary-color: #your-color;
  --accent-color: #your-color;
}
```

**CMS Colors** (`htdocs/themes/cms/modern/assets/css/theme.css`):
```css
:root {
  --cms-primary: #your-color;
  --cms-secondary: #your-color;
  --cms-accent: #your-color;
}
```

### **2. Update Site Logo**

**Framework Logo** (`htdocs/app/config.php`):
```php
'logo_small' => '/assets/images/your-logo.png',
'theme_config' => [
    'logo' => '/assets/images/your-logo.png',
    // ...
],
```

**CMS Logo** (in CMS page templates):
```php
<img src="/themes/cms/modern/assets/images/logo.png" alt="Site Logo">
```

### **3. Customize Page Templates**

**Edit CMS Page Layout** (`htdocs/themes/cms/modern/templates/default.php`):
```php
<!DOCTYPE html>
<html>
<head>
    <title><?= $page['meta_title'] ?? $page['title'] ?></title>
    <meta name="description" content="<?= $page['meta_description'] ?>">
    <!-- Your custom head content -->
</head>
<body>
    <header>
        <!-- Your custom header -->
    </header>
    
    <main>
        <h1><?= $page['title'] ?></h1>
        <div class="content">
            <?= $page['content'] ?>
        </div>
    </main>
    
    <footer>
        <!-- Your custom footer -->
    </footer>
</body>
</html>
```

### **4. Add Custom CSS/JS**

**Framework Assets** (`htdocs/themes/default/theme.json`):
```json
{
  "css": "/css/your-custom.css",
  "js": "/js/your-custom.js"
}
```

**CMS Assets** (`htdocs/themes/cms/modern/theme.json`):
```json
{
  "assets": {
    "css": [
      "assets/css/theme.css",
      "assets/css/your-custom.css"
    ],
    "js": [
      "assets/js/your-custom.js"
    ]
  }
}
```

---

## 📁 **File Organization**

```
Your StrataPHP Site
├── Framework Theme (Admin/Auth pages)
│   └── htdocs/themes/default/
│       ├── theme.json          ← Framework theme config
│       └── assets/css/styles.css ← Admin styling
│
└── CMS Theme (Public content)
    └── htdocs/themes/cms/modern/
        ├── theme.json          ← CMS theme config  
        ├── templates/          ← Page layouts
        │   ├── default.php     ← Update layouts here
        │   └── full-width.php
        └── assets/             ← CMS assets
            ├── css/theme.css   ← Update CMS styling here
            └── js/theme.js
```

---

## ⚡ **Quick Tips**

1. **Start with CSS customization** before changing templates
2. **Test changes on different screen sizes**
3. **Keep backups** of original files before customizing
4. **Use browser dev tools** to preview changes
5. **Check the complete guide** at `docs/THEME_SYSTEM_GUIDE.md` for advanced customization

---

**Happy customizing!** 🎨