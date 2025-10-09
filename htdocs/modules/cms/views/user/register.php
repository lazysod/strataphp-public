<?php
/**
 * CMS-Themed User Registration Page
 */

use App\Modules\Cms\UserThemeWrapper;

// Generate the registration form content
$formContent = '
<form id="userRegisterForm" method="post" action="/user/register" style="margin: 0;">
    <input type="hidden" name="token" value="' . htmlspecialchars(\App\TokenManager::csrf()) . '">
    
    <div style="margin-bottom: 1.5rem;">
        <label for="display_name" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--secondary-color);">Display Name</label>
        <input class="cms-input" id="display_name" name="display_name" type="text" placeholder="Your display name" required 
               style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: 8px; font-size: 1rem; transition: border-color 0.3s ease;" />
    </div>
    
    <div style="margin-bottom: 1.5rem;">
        <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--secondary-color);">Email Address</label>
        <input class="cms-input" id="email" name="email" type="email" placeholder="your@email.com" required 
               style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: 8px; font-size: 1rem; transition: border-color 0.3s ease;" />
    </div>
    
    <div style="margin-bottom: 1.5rem;">
        <label for="password" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--secondary-color);">Password</label>
        <input class="cms-input" id="password" name="password" type="password" placeholder="Enter a secure password" required 
               style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: 8px; font-size: 1rem; transition: border-color 0.3s ease;" />
    </div>
    
    <div style="margin-bottom: 2rem;">
        <label for="confirm_password" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--secondary-color);">Confirm Password</label>
        <input class="cms-input" id="confirm_password" name="confirm_password" type="password" placeholder="Confirm your password" required 
               style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: 8px; font-size: 1rem; transition: border-color 0.3s ease;" />
    </div>
    
    <button class="cms-btn-primary" type="submit" id="submitButton" 
            style="width: 100%; padding: 1rem; background: var(--gradient-primary); color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
        🚀 Create Account
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
    <p style="margin: 0.5rem 0;">Already have an account? <a href="/user/login" style="color: var(--primary-color); font-weight: 600;">Login here</a></p>
    <p style="margin: 0.5rem 0; color: var(--text-light);">
        By registering, you agree to our <a href="/terms" style="color: var(--primary-color);">Terms of Service</a> 
        and <a href="/privacy" style="color: var(--primary-color);">Privacy Policy</a>.
    </p>
</div>';

// Generate the full page content
$pageContent = UserThemeWrapper::generateFormContent('Create Your Account', $formContent, $error ?? '', $success ?? '');

// Render using CMS theme
UserThemeWrapper::renderUserPage('User Registration', $pageContent, [
    'description' => 'Create a new account to access all features',
    'title' => 'Register | StrataPHP CMS',
    'slug' => 'register',
    'noindex' => true
]);
?>