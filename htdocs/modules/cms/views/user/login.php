<?php
/**
 * CMS-Themed User Login Page
 */

use App\Modules\Cms\UserThemeWrapper;

// Generate the login form content
$formContent = '
<form id="userLoginForm" method="post" action="/user/login" style="margin: 0;">
    <input type="hidden" name="token" value="' . htmlspecialchars(\App\TokenManager::csrf()) . '">
    
    <div style="margin-bottom: 1.5rem;">
        <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--secondary-color);">Email Address</label>
        <input class="cms-input" id="email" name="email" type="email" placeholder="your@email.com" required 
               style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: 8px; font-size: 1rem; transition: border-color 0.3s ease;" />
    </div>
    
    <div style="margin-bottom: 2rem;">
        <label for="password" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--secondary-color);">Password</label>
        <input class="cms-input" id="password" name="password" type="password" placeholder="Enter your password" required 
               style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: 8px; font-size: 1rem; transition: border-color 0.3s ease;" />
    </div>
    
    <button class="cms-btn-primary" type="submit" id="submitButton" 
            style="width: 100%; padding: 1rem; background: var(--gradient-primary); color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
        🔐 Sign In
    </button>
</form>

<style>
.cms-input:focus {
    outline: none;
    border-color: var(--primary-color) !important;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.cms-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}
</style>

<div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); text-align: center; font-size: 0.9rem;">
    <p style="margin: 0.5rem 0;">Don\'t have an account? <a href="/user/register" style="color: var(--primary-color); font-weight: 600;">Register here</a></p>
    <p style="margin: 0.5rem 0;"><a href="/user/reset-request" style="color: var(--primary-color);">Forgot your password?</a></p>
</div>';

// Generate the full page content
$pageContent = UserThemeWrapper::generateFormContent('Welcome Back', $formContent, $error ?? '', $success ?? '');

// Render using CMS theme
UserThemeWrapper::renderUserPage('User Login', $pageContent, [
    'description' => 'Sign in to your account to access all features',
    'title' => 'Login | StrataPHP CMS',
    'slug' => 'login',
    'noindex' => true
]);
?>