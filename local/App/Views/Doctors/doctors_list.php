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
        <h1>Список врачей</h1>
        <nav>
            <a href="<?= $baseUrl ?>?action=add_doctor" class="btn">Создать врача</a>
            <a href="<?= $baseUrl ?>?action=add_procedure" class="btn">Создать процедуру</a>
            <a href="<?= $baseUrl ?>?action=install" class="btn btn-edit">Переустановить данные</a>
            <a href="<?= $baseUrl ?>?action=check_lists" class="btn btn-primary">Проверить списки</a>
        </nav>
    </header>

    <div class="doctors-list">
        <?php if (empty($doctors)): ?>
            <div class="empty-state">
                <p>Врачи не найдены.</p>
                <a href="<?= $baseUrl ?>?action=install" class="btn">Установить тестовые данные</a>
            </div>
        <?php else: ?>
            <?php foreach ($doctors as $doctor): ?>
                <div class="doctor-card">
                    <div class="doctor-info">
                        <h3><?= htmlspecialchars($doctor['FULL_NAME']) ?></h3>
                        <p class="doctor-meta">
                            <strong>Процедур:</strong> <?= count($doctor['PROCEDURE_NAMES']) ?>
                        </p>
                        <?php if (!empty($doctor['PROCEDURE_NAMES'])): ?>
                            <p class="procedures-preview">
                                <?= implode(', ', array_slice($doctor['PROCEDURE_NAMES'], 0, 3)) ?>
                                <?php if (count($doctor['PROCEDURE_NAMES']) > 3): ?>...<?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="doctor-actions">
                        <a href="<?= $baseUrl ?>?action=view&doctor_id=<?= $doctor['ID'] ?>"
                           class="btn btn-sm">Процедуры</a>
                        <a href="<?= $baseUrl ?>?action=edit&doctor_id=<?= $doctor['ID'] ?>"
                           class="btn btn-sm btn-edit">Редактировать</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>