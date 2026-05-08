# Migrate from Lumen to StrataPHP

Lumen was a great micro-framework, but it is now end-of-life. StrataPHP continues the micro-framework philosophy with active development and a modular, PSR-friendly approach. This guide provides a realistic overview of what to expect when migrating from Lumen to StrataPHP.

## 1. Routing
StrataPHP uses a similar routing style to Lumen:

**Lumen:**
```php
$router->get('/api/users', function () {
    return response()->json(User::all());
});
```

**StrataPHP:**
```php
$router->get('/api/users', function () {
    return $this->jsonResponse(User::all());
});
```
**Note:** StrataPHP uses a `jsonResponse()` method, not `new JsonResponse()` or `response()->json()`.

## 2. Service Container
StrataPHP does not use PSR-11 or `Illuminate\Container` by default. If you need a service container, you can integrate one, but it is not included out of the box.

## 3. Database Access
StrataPHP uses its own `DB` class (PDO-based) for database access. Eloquent ORM (`illuminate/database`) is not included by default. You can add Eloquent manually if needed, but it is not a core feature.

## 4. PSR-7 Support
StrataPHP does not provide native PSR-7 support out of the box. If you require PSR-7, you will need to integrate a compatible library yourself.

## 5. Core Size & Boot Time
StrataPHP is lightweight and has a smaller vendor directory than Lumen. However, specific size and boot time numbers are not benchmarked here.

## 6. Modules
StrataPHP includes a modular system. Core modules like Admin, CMS, and User are available and can be enabled or disabled in configuration.

## 7. Migration Steps
1. Create a new StrataPHP project skeleton.
2. Copy your Lumen `routes/web.php` to StrataPHP's routes.
3. Copy your `app/Models/` directory if you use plain models.
4. Adapt your service providers and configuration as needed.
5. Update database access to use StrataPHP's `DB` class or integrate Eloquent if required.
6. Run your tests and verify functionality.

**Note:** Migration is not always "drop-in"—some adaptation is required, especially for database and service container usage.

## 8. What You Gain
- Active maintenance and PHP 8.2+ compatibility
- Smaller, modular codebase
- Built-in modules for admin, CMS, and more

## 9. Need Help?
If you get stuck, post your `routes/web.php` or questions in the community Discord for help porting.
