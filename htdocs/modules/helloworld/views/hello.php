<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello World - StrataPHP</title>
    <link rel="stylesheet" href="/modules/helloworld/assets/style.css">
</head>
<body>
    <?php
    use App\Modules\HelloWorld\Models\HelloWorld;

    $model = new HelloWorld();
    echo '<h1>' . htmlspecialchars($model->getMessage()) . '</h1>';
    echo '<p>This is a simple demonstration of a StrataPHP module.</p>';
    echo '<p><strong>Random message:</strong> ' . htmlspecialchars($model->getMessage(true)) . '</p>';
    echo '<p><a href="/">← Back to Home</a> | <a href="/hello">🔄 Refresh for random message</a></p>';
    ?>
</body>
</html>
