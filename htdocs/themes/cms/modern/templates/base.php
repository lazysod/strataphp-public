<?php
/**
 * Base CMS Page Template
 * 
 * Default template for CMS pages with modern responsive design
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
    
    <?php if (isset($meta['canonical'])): ?>
    <link rel="canonical" href="<?= htmlspecialchars($meta['canonical']) ?>">
    <?php endif; ?>
    
    <!-- Open Graph Meta Tags -->
    <?php if (isset($meta['og_title'])): ?>
    <meta property="og:title" content="<?= htmlspecialchars($meta['og_title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($meta['og_description'] ?? '') ?>">
    <meta property="og:type" content="<?= htmlspecialchars($meta['og_type'] ?? 'website') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($meta['og_url'] ?? '') ?>">
    <?php endif; ?>
    
    <!-- Theme Styles -->
    <style>
        <?= (new App\Modules\Cms\ThemeManager())->generateThemeCSS() ?>
        
        /* Base Layout Styles */
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
        
        .cms-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .cms-header {
            background: var(--primary-color);
            color: white;
            padding: 20px 0;
            margin-bottom: 40px;
        }
        
        .cms-header h1 {
            color: white;
            font-size: 2.5rem;
            font-weight: 300;
            margin: 0;
        }
        
        .cms-nav {
            background: var(--secondary-color);
            padding: 15px 0;
            margin-bottom: 40px;
        }
        
        .cms-nav ul {
            list-style: none;
            display: flex;
            gap: 30px;
            align-items: center;
        }
        
        .cms-nav a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .cms-nav a:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .cms-main {
            min-height: 60vh;
            margin-bottom: 60px;
        }
        
        .cms-content {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .cms-content h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: var(--secondary-color);
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 15px;
        }
        
        .cms-content h2 {
            font-size: 2rem;
            margin: 30px 0 15px 0;
            color: var(--secondary-color);
        }
        
        .cms-content h3 {
            font-size: 1.5rem;
            margin: 25px 0 10px 0;
            color: var(--secondary-color);
        }
        
        .cms-content p {
            margin-bottom: 15px;
            line-height: 1.7;
        }
        
        .cms-content ul, .cms-content ol {
            margin: 15px 0 15px 30px;
        }
        
        .cms-content li {
            margin-bottom: 5px;
        }
        
        .cms-content img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            margin: 20px 0;
        }
        
        .cms-content blockquote {
            border-left: 4px solid var(--primary-color);
            padding: 20px;
            margin: 20px 0;
            background: #f8f9fa;
            font-style: italic;
        }
        
        .cms-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .cms-content th,
        .cms-content td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .cms-content th {
            background: var(--primary-color);
            color: white;
            font-weight: 600;
        }
        
        .cms-footer {
            background: var(--secondary-color);
            color: white;
            padding: 40px 0;
            text-align: center;
            margin-top: 60px;
        }
        
        .cms-footer p {
            margin: 0;
            opacity: 0.8;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .cms-container {
                padding: 0 15px;
            }
            
            .cms-content {
                padding: 20px;
            }
            
            .cms-content h1 {
                font-size: 2rem;
            }
            
            .cms-nav ul {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
    
    <?php if (isset($theme['css_url']) && file_exists($theme['css_url'])): ?>
    <link rel="stylesheet" href="<?= $theme['css_url'] ?>">
    <?php endif; ?>
</head>
<body>
    <header class="cms-header">
        <div class="cms-container">
            <h1>StrataPHP CMS</h1>
        </div>
    </header>
    
    <nav class="cms-nav">
        <div class="cms-container">
            <ul>
                <?php if (isset($navigation) && !empty($navigation)): ?>
                    <?php foreach ($navigation as $navItem): ?>
                        <li>
                            <a href="<?= htmlspecialchars($navItem['url']) ?>"
                               <?= (!empty($page['slug']) && $page['slug'] === $navItem['slug']) ? 'style="background: rgba(255,255,255,0.1);"' : '' ?>>
                                <?= htmlspecialchars($navItem['title']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback navigation if no CMS pages found -->
                    <li><a href="/">Home</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <main class="cms-main">
        <div class="cms-container">
            <article class="cms-content">
                <h1><?= htmlspecialchars($page['title']) ?></h1>
                
                <?php if (!empty($page['excerpt'])): ?>
                <div class="cms-excerpt" style="font-size: 1.1rem; color: #666; margin-bottom: 30px; font-style: italic;">
                    <?= htmlspecialchars($page['excerpt']) ?>
                </div>
                <?php endif; ?>
                
                <div class="cms-page-content">
                    <?= $page['content'] ?>
                </div>
                
                <div class="cms-meta" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 14px; color: #666;">
                    <?php if (isset($page['created_at'])): ?>
                    <p>Published: <?= date('F j, Y', strtotime($page['created_at'])) ?></p>
                    <?php endif; ?>
                    
                    <?php if (isset($page['updated_at']) && $page['updated_at'] !== $page['created_at']): ?>
                    <p>Last updated: <?= date('F j, Y', strtotime($page['updated_at'])) ?></p>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    </main>
    
    <footer class="cms-footer">
        <div class="cms-container">
            <p>&copy; <?= date('Y') ?> StrataPHP CMS. Powered by StrataPHP Framework.</p>
        </div>
    </footer>
    
    <?php if (isset($theme['js_url']) && file_exists($theme['js_url'])): ?>
    <script src="<?= $theme['js_url'] ?>"></script>
    <?php endif; ?>
</body>
</html>