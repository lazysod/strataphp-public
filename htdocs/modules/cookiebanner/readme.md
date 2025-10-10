# Cookie Banner Plugin

A simple plugin for StrataPHP CMS to display a cookie consent banner.

## Features
- Customizable message
- Configurable cookie name and duration
- Optional link to a privacy/read more page

## Usage
1. Place the `CookieBanner.php` and `config.php` in `modules/cookiebanner/`.
2. In your theme or layout, add:

```php
$cookieConfig = include __DIR__ . '/modules/cookiebanner/config.php';
$banner = new \App\Modules\CookieBanner\CookieBanner($cookieConfig);
echo $banner->render();
```

3. Edit `config.php` to change the message, cookie name, duration, or read more link.
