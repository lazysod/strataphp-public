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
    <meta property="og:site_name" content="<?= htmlspecialchars($meta['site_name'] ?? 'StrataPHP CMS') ?>">
    <?php endif; ?>
    
    <?php if (!empty($page['og_image'])): ?>
    <meta property="og:image" content="<?= htmlspecialchars($page['og_image']) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/png">
    <?php 
    // Generate thumbnail URL for additional meta tag
    $thumbnailUrl = str_replace('/storage/uploads/cms/', '/storage/uploads/cms/thumbs/', $page['og_image']);
    ?>
    <meta property="og:image:thumbnail" content="<?= htmlspecialchars($thumbnailUrl) ?>">
    <?php endif; ?>
    
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="<?= htmlspecialchars($page['twitter_card'] ?? 'summary_large_image') ?>">
    <?php if (isset($meta['og_title'])): ?>
    <meta name="twitter:title" content="<?= htmlspecialchars($meta['og_title']) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($meta['og_description'] ?? '') ?>">
    <?php endif; ?>
    <?php if (!empty($page['og_image'])): ?>
    <meta name="twitter:image" content="<?= htmlspecialchars($page['og_image']) ?>">
    <meta name="twitter:image:alt" content="<?= htmlspecialchars($meta['og_title'] ?? $page['title'] ?? 'Page image') ?>">
    <?php endif; ?>
    
    <?php if (!empty($page['noindex'])): ?>
    <meta name="robots" content="noindex, nofollow">
    <?php endif; ?>
    
    <!-- Theme Styles -->
    <style>
        <?= (new App\Modules\Cms\ThemeManager())->generateThemeCSS() ?>
        
        /* Enhanced Modern Theme */
        :root {
            --text-light: #6c757d;
            --border-color: #e9ecef;
            --shadow-light: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-medium: 0 4px 12px rgba(0,0,0,0.1);
            --shadow-strong: 0 8px 25px rgba(0,0,0,0.15);
            --gradient-primary: linear-gradient(135deg, var(--primary-color) 0%, #2980b9 100%);
            --gradient-secondary: linear-gradient(135deg, var(--secondary-color) 0%, #34495e 100%);
        }

        /* Reset and Base Styles */
        *, *::before, *::after {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-family);
            color: var(--secondary-color);
            line-height: 1.7;
            background: #f8f9fa;
            font-size: 16px;
            margin: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Typography Enhancements */
        h1, h2, h3, h4, h5, h6 {
            color: var(--secondary-color);
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 1rem;
        }

        h1 { font-size: 2.5rem; }
        h2 { font-size: 2rem; }
        h3 { font-size: 1.5rem; }
        h4 { font-size: 1.25rem; }
        h5 { font-size: 1.125rem; }
        h6 { font-size: 1rem; }

        p {
            margin-bottom: 1.5rem;
            line-height: 1.7;
        }

        a {
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        /* Layout Components */
        .cms-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .cms-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem 0;
            box-shadow: var(--shadow-medium);
            position: relative;
            overflow: hidden;
        }

        .cms-header .cms-container {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cms-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            pointer-events: none;
        }

        .cms-header h1 {
            color: white;
            font-size: 2.5rem;
            font-weight: 300;
            margin: 0;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Hamburger Menu Button */
        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 0.5rem;
            background: none;
            border: none;
            z-index: 101;
            position: relative;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: white;
            margin: 3px 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }
        
        .cms-nav {
            background: var(--gradient-secondary);
            padding: 0;
            box-shadow: var(--shadow-light);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .cms-nav ul {
            list-style: none;
            display: flex;
            gap: 0;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .cms-nav li {
            position: relative;
        }

        .cms-nav a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            padding: 1rem 1.5rem;
            border-radius: 0;
            transition: all 0.3s ease;
            display: block;
            font-weight: 500;
            position: relative;
        }

        .cms-nav a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 3px;
            background: var(--accent-color);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .cms-nav a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
        }

        .cms-nav a:hover::before,
        .cms-nav a[style*="background"]::before {
            width: 80%;
        }

        .cms-main {
            min-height: 70vh;
            padding: 3rem 0;
        }

        .cms-content {
            background: white;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: var(--shadow-strong);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .cms-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .cms-content h1 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: var(--secondary-color);
            position: relative;
            padding-bottom: 1rem;
        }

        .cms-content h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary-color);
            border-radius: 2px;
        }
        
        .cms-content h2 {
            font-size: 2rem;
            margin: 2rem 0 1rem 0;
            color: var(--secondary-color);
            position: relative;
            padding-left: 1rem;
        }

        .cms-content h2::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 4px;
            height: 1.5rem;
            background: var(--primary-color);
            transform: translateY(-50%);
            border-radius: 2px;
        }

        .cms-content h3 {
            font-size: 1.5rem;
            margin: 1.5rem 0 0.75rem 0;
            color: var(--secondary-color);
        }

        .cms-content h4, .cms-content h5, .cms-content h6 {
            margin: 1rem 0 0.5rem 0;
            color: var(--secondary-color);
        }

        .cms-content p {
            margin-bottom: 1.5rem;
            line-height: 1.8;
            color: #4a5568;
        }

        .cms-content ul, .cms-content ol {
            margin: 1.5rem 0 1.5rem 2rem;
            color: #4a5568;
        }

        .cms-content li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }

        .cms-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 2rem 0;
            box-shadow: var(--shadow-medium);
            transition: transform 0.3s ease;
        }

        .cms-content img:hover {
            transform: scale(1.02);
        }

        .cms-content blockquote {
            border-left: 4px solid var(--primary-color);
            padding: 1.5rem;
            margin: 2rem 0;
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.05) 0%, rgba(52, 152, 219, 0.02) 100%);
            font-style: italic;
            border-radius: 0 8px 8px 0;
            position: relative;
        }

        .cms-content blockquote::before {
            content: '"';
            font-size: 4rem;
            color: var(--primary-color);
            position: absolute;
            top: -10px;
            left: 15px;
            opacity: 0.3;
            font-family: Georgia, serif;
        }

        .cms-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 2rem 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow-medium);
        }

        .cms-content th,
        .cms-content td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .cms-content th {
            background: var(--gradient-primary);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.875rem;
        }

        .cms-content tr:nth-child(even) {
            background: rgba(248, 249, 250, 0.5);
        }

        .cms-content tr:hover {
            background: rgba(52, 152, 219, 0.05);
            transition: background-color 0.3s ease;
        }

        /* Enhanced excerpt styling */
        .cms-excerpt {
            font-size: 1.1rem !important;
            color: var(--text-light) !important;
            margin-bottom: 2rem !important;
            font-style: italic !important;
            padding: 1.5rem !important;
            border-left: 4px solid var(--primary-color) !important;
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.08) 0%, rgba(52, 152, 219, 0.03) 100%) !important;
            border-radius: 0 8px 8px 0 !important;
            position: relative !important;
        }

        .cms-excerpt::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gradient-primary);
        }
        
        .cms-footer {
            background: var(--gradient-secondary);
            color: white;
            padding: 3rem 0;
            text-align: center;
            margin-top: 4rem;
            position: relative;
            overflow: hidden;
        }

        .cms-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="footergrid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23footergrid)"/></svg>');
            pointer-events: none;
        }

        .cms-footer p {
            margin: 0;
            opacity: 0.9;
            position: relative;
            z-index: 1;
            font-size: 0.9rem;
        }

        /* Meta information styling */
        .cms-meta {
            margin-top: 3rem !important;
            padding-top: 2rem !important;
            border-top: 2px solid var(--border-color) !important;
            font-size: 0.875rem !important;
            color: var(--text-light) !important;
            background: rgba(248, 249, 250, 0.5) !important;
            padding: 1.5rem !important;
            border-radius: 8px !important;
            margin-left: -3rem !important;
            margin-right: -3rem !important;
        }

        .cms-meta p {
            margin-bottom: 0.5rem !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .cms-meta p::before {
            content: '📅';
            font-size: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .cms-container {
                padding: 0 1rem;
            }
            
            .cms-content {
                padding: 2rem 1.5rem;
                border-radius: 8px;
            }
            
            .cms-content h1 {
                font-size: 2rem;
            }
            
            .cms-header h1 {
                font-size: 2rem;
            }

            /* Mobile Navigation */
            .hamburger {
                display: flex;
            }

            .cms-nav ul {
                position: fixed;
                top: 0;
                right: -100%;
                width: 80%;
                max-width: 300px;
                height: 100vh;
                background: var(--gradient-secondary);
                flex-direction: column;
                gap: 0;
                text-align: left;
                transition: right 0.3s ease;
                padding-top: 80px;
                box-shadow: -5px 0 15px rgba(0,0,0,0.1);
                z-index: 100;
            }

            .cms-nav ul.active {
                right: 0;
            }

            .cms-nav li {
                width: 100%;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }

            .cms-nav a {
                padding: 1.25rem 2rem;
                border-bottom: none;
                width: 100%;
                font-size: 1.1rem;
            }

            .cms-nav a::before {
                display: none;
            }

            .cms-nav a:hover {
                background: rgba(255, 255, 255, 0.15);
                padding-left: 2.5rem;
            }

            /* Overlay for mobile menu */
            .mobile-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 99;
                transition: opacity 0.3s ease;
            }

            .mobile-overlay.active {
                display: block;
            }

            .cms-main {
                padding: 2rem 0;
            }

            .cms-meta {
                margin-left: -1.5rem !important;
                margin-right: -1.5rem !important;
            }
        }

        @media (max-width: 480px) {
            .cms-content {
                padding: 1.5rem 1rem;
            }

            .cms-content h1 {
                font-size: 1.75rem;
            }

            .cms-content h2 {
                font-size: 1.5rem;
            }

            .cms-header {
                padding: 1.5rem 0;
            }

            .cms-header h1 {
                font-size: 1.75rem;
            }

            .cms-meta {
                margin-left: -1rem !important;
                margin-right: -1rem !important;
            }
        }

        /* Utility classes */
        .text-center { text-align: center; }
        .text-muted { color: var(--text-light); }
        
        /* Animation for page load */
        .cms-content {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
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
            <button class="hamburger" onclick="toggleMobileMenu()" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>
    
    <nav class="cms-nav">
        <div class="cms-container">
            <ul id="nav-menu">
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
        <div class="mobile-overlay" id="mobile-overlay" onclick="closeMobileMenu()"></div>
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
    
    <script>
        // Mobile menu functionality
        function toggleMobileMenu() {
            const hamburger = document.querySelector('.hamburger');
            const navMenu = document.getElementById('nav-menu');
            const overlay = document.getElementById('mobile-overlay');
            
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
            overlay.classList.toggle('active');
            
            // Prevent body scrolling when menu is open
            document.body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : '';
        }
        
        function closeMobileMenu() {
            const hamburger = document.querySelector('.hamburger');
            const navMenu = document.getElementById('nav-menu');
            const overlay = document.getElementById('mobile-overlay');
            
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
            overlay.classList.remove('active');
            
            // Restore body scrolling
            document.body.style.overflow = '';
        }
        
        // Close menu when clicking on navigation links
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.cms-nav a');
            navLinks.forEach(link => {
                link.addEventListener('click', closeMobileMenu);
            });
            
            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeMobileMenu();
                }
            });
            
            // Close menu on window resize to desktop size
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    closeMobileMenu();
                }
            });
        });
    </script>
</body>
</html>