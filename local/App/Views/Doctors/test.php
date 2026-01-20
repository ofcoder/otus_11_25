<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест путей</title>
    <link rel="stylesheet" href="<?= $assetsUrl ?>style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Тестирование путей</h1>
    </header>
    
    <div class="debug-info">
        <h2>Доступные переменные:</h2>
        <ul>
            <li><strong>baseUrl:</strong> <?= htmlspecialchars($baseUrl ?? 'не определена') ?></li>
            <li><strong>assetsUrl:</strong> <?= htmlspecialchars($assetsUrl ?? 'не определена') ?></li>
            <li><strong>title:</strong> <?= htmlspecialchars($title ?? 'не определена') ?></li>
        </ul>
        
        <h2>Пути к файлам:</h2>
        <ul>
            <li>Стили: <?= $assetsUrl ?>style.css</li>
            <li>Главная: <?= $baseUrl ?></li>
            <li>Добавить врача: <?= $baseUrl ?>?action=add_doctor</li>
        </ul>
        
        <h2>Действия:</h2>
        <div class="actions">
            <a href="<?= $baseUrl ?>" class="btn">На главную</a>
            <a href="<?= $baseUrl ?>?action=install" class="btn">Тест установки</a>
        </div>
    </div>
</div>
</body>
</html>