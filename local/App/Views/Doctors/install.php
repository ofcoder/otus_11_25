<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="<?= $assetsUrl ?>style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Установка данных</h1>
        <nav>
            <a href="<?= $baseUrl ?>" class="btn btn-primary">Назад к списку</a>
            <a href="<?= $baseUrl ?>force-install.php" class="btn">Принудительная переустановка</a>
        </nav>
    </header>

    <div class="install-result">
        <?php if ($result['success']): ?>
            <div class="success">
                <h3>Установка завершена успешно!</h3>
                <?php if (!empty($result['messages'])): ?>
                    <ul>
                        <?php foreach ($result['messages'] as $message): ?>
                            <li><?= htmlspecialchars($message) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="error">
                <h3>Ошибки при установке:</h3>
                <?php if (!empty($result['errors'])): ?>
                    <ul>
                        <?php foreach ($result['errors'] as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php if (!empty($result['messages'])): ?>
                    <h4>Сообщения:</h4>
                    <ul>
                        <?php foreach ($result['messages'] as $message): ?>
                            <li><?= htmlspecialchars($message) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="actions">
            <a href="<?= $baseUrl ?>" class="btn btn-primary">Перейти к списку врачей</a>
            <a href="<?= $baseUrl ?>force-install.php" class="btn">Принудительная переустановка</a>
        </div>
    </div>
</div>
</body>
</html>