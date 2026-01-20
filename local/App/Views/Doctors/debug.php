<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отладка установки</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Отладка установки приложения</h1>
        <nav>
            <a href="<?= $baseUrl ?>" class="btn">На главную</a>
        </nav>
    </header>
    
    <div class="debug-info">
        <h2>Проверка окружения:</h2>
        <ul>
            <li>Модуль iblock: <?= extension_loaded('iblock') ? '✓' : '✗' ?></li>
            <li>Версия PHP: <?= phpversion() ?></li>
            <li>Память: <?= ini_get('memory_limit') ?></li>
        </ul>
        
        <h2>Проверка инфоблоков:</h2>
        <ul>
            <li>Инфоблок doctors: <?= $doctorsIblockId ? '✓ (ID: ' . $doctorsIblockId . ')' : '✗' ?></li>
            <li>Инфоблок procedures: <?= $proceduresIblockId ? '✓ (ID: ' . $proceduresIblockId . ')' : '✗' ?></li>
        </ul>
        
        <h2>Действия:</h2>
        <div class="actions">
            <a href="<?= $baseUrl ?>?action=install" class="btn">Установить данные</a>
            <a href="<?= $baseUrl ?>?action=clear_cache" class="btn">Очистить кэш</a>
        </div>
    </div>
</div>
</body>
</html>