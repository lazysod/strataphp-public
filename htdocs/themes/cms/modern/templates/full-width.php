<?php
/**
 * Full-Width Page Template
 * 
 * Full-width layout without sidebar constraints
 */

// Get page meta data
$meta = isset($theme) ? (new App\Modules\Cms\ThemeManager())->getPageMeta($page) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($meta['title'] ?? $page['title'] ?? 'Page') ?></title>
    
    <?php if (isset($meta['description'])): ?>
    <meta name="description" content="<?= htmlspecialchars($meta['description']) ?>">
    <?php endif; ?>
    
    <!-- Theme Styles -->
    <style>
        <?= (new App\Modules\Cms\ThemeManager())->generateThemeCSS() ?>
        
        /* Full-Width Layout Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #ffffff;
            font-size: 16px;
            margin: 0;
        }
        
        .cms-full-width {
            width: 100%;
            padding: 0;
        }
        
        .cms-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 60px 0;
            text-align: center;
        }
        
        .cms-header h1 {
            color: white;
            font-size: 3.5rem;
            font-weight: 300;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .cms-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 60px 40px;
        }
        
        .cms-content h1 {
            display: none; /* Hidden since it's in the header */
        }
        
        .cms-content h2 {
            font-size: 2.5rem;
            margin: 40px 0 20px 0;
            color: var(--secondary-color);
            text-align: center;
        }
        
        .cms-content p {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 20px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .cms-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 40px auto;
            display: block;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .cms-header h1 {
                font-size: 2.5rem;
            }
            
            .cms-content {
                padding: 40px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="cms-full-width">
        <header class="cms-header">
            <h1><?= htmlspecialchars($page['title']) ?></h1>
            <?php if (!empty($page['excerpt'])): ?>
            <p style="font-size: 1.2rem; margin-top: 20px; opacity: 0.9;">
                <?= htmlspecialchars($page['excerpt']) ?>
            </p>
            <?php endif; ?>
        </header>
        
        <main class="cms-content">
            <?= $page['content'] ?>
        </main>
    </div>
</body>
</html>