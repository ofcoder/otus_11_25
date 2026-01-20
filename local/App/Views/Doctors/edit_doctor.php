<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="<?= $assetsUrl ?>style.css">
    <style>
        .selected-procedures {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .selected-procedures ul {
            margin: 10px 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1><?= $title ?></h1>
        <nav>
            <a href="<?= $baseUrl ?>" class="btn btn-primary">Назад к списку</a>
            <a href="<?= $baseUrl ?>?action=view&doctor_id=<?= $doctor['ID'] ?>" class="btn">Просмотр</a>
            <a href="<?= $baseUrl ?>?action=delete&doctor_id=<?= $doctor['ID'] ?>"
               class="btn btn-danger"
               onclick="return confirm('Вы уверены, что хотите удалить этого врача?')">Удалить</a>
        </nav>
    </header>
    
    <?php if (!empty($doctor['PROCEDURES'])): ?>
        <div class="selected-procedures">
            <h3>Текущие процедуры врача:</h3>
            <ul>
                <?php foreach ($doctor['PROCEDURES'] as $procedure): ?>
                    <li><?= htmlspecialchars($procedure) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" class="add-form">
        <div class="form-group">
            <label for="last_name">Фамилия:</label>
            <input type="text" id="last_name" name="last_name"
                   value="<?= htmlspecialchars($doctor['LAST_NAME'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="first_name">Имя:</label>
            <input type="text" id="first_name" name="first_name"
                   value="<?= htmlspecialchars($doctor['FIRST_NAME'] ?? '') ?>" required>
        </div>

        
        <div class="form-group">
            <label for="second_name">Отчество:</label>
            <input type="text" id="second_name" name="second_name"
                   value="<?= htmlspecialchars($doctor['SECOND_NAME'] ?? '') ?>">
        </div>
        <!--
        <div class="form-group">
            <label for="procedures">Процедуры:</label>
            <select id="procedures" name="procedures[]" multiple size="8">
                <?php foreach ($procedures as $procedure): ?>
                    <option value="<?= $procedure['ID'] ?>"
                        <?= in_array($procedure['ID'], $doctor['PROCEDURE_IDS'] ?? []) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($procedure['NAME']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small>Для выбора нескольких процедур удерживайте Ctrl (Cmd на Mac)</small>
        </div>
        -->
        <!-- Вместо select с multiple -->
        <div class="form-group">
            <label>Процедуры:</label>
            <div class="procedures-checkboxes">
                <?php foreach ($procedures as $procedure): ?>
                    <div class="checkbox-item">
                        <label>
                            <input type="checkbox"
                                   name="procedures[]"
                                   value="<?= $procedure['ID'] ?>"
                                <?= in_array($procedure['ID'], $doctor['PROCEDURES_IDS'] ?? []) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($procedure['NAME']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="<?= $baseUrl ?>" class="btn">Отмена</a>
        </div>
    </form>
</div>
</body>
</html>