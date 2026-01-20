<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ошибка</title>
    <link rel="stylesheet" href="<?= $assetsUrl ?>style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Ошибка</h1>
        <nav>
            <a href="<?= $baseUrl ?>" class="btn">На главную</a>
        </nav>
    </header>

    <div class="error">
        <p><?= htmlspecialchars($error) ?></p>
    </div>
</div>
</body>
</html>