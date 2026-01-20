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
        <h1>Процедуры врача: <?= htmlspecialchars($doctor['FULL_NAME']) ?></h1>
        <nav>
            <a href="<?= $baseUrl ?>" class="btn  btn-primary">Назад к списку</a>
            <a href="<?= $baseUrl ?>?action=add_procedure" class="btn">Создать процедуру</a>
            <a href="<?= $baseUrl ?>?action=add_doctor" class="btn">Создать врача</a>
            <a href="<?= $baseUrl ?>?action=edit&doctor_id=<?= $doctor['ID'] ?>" class="btn btn-edit">Редактировать</a>
        </nav>
    </header>

    <div class="procedures-list">
        <?php if (empty($doctor['PROCEDURES'])): ?>
            <p>У врача нет назначенных процедур</p>
        <?php else: ?>
            <ul>
                <?php foreach ($doctor['PROCEDURES'] as $procedure): ?>
                    <li><?= htmlspecialchars($procedure) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
</body>
</html>