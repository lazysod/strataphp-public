# StrataPHP

## Hello World
```php
<?php
require 'vendor/autoload.php';
$app = new Strata\App();
$app->get('/', fn() => 'Hello StrataPHP v1.0.0');
$app->run();