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
        <h1>Добавить врача</h1>
        <nav>
            <a href="<?= $baseUrl ?>" class="btn btn-primary">Назад к списку</a>
        </nav>
    </header>

    <form method="POST" class="add-form">
        <div class="form-group">
            <label for="last_name">Фамилия:</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>

        <div class="form-group">
            <label for="first_name">Имя:</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>

        <div class="form-group">
            <label for="second_name">Отчество:</label>
            <input type="text" id="second_name" name="second_name">
        </div>

        <div class="form-group">
            <label for="procedures">Процедуры:</label>
            <select id="procedures" name="procedures[]" multiple>
                <?php foreach ($procedures as $procedure): ?>
                    <option value="<?= $procedure['ID'] ?>"><?= htmlspecialchars($procedure['NAME']) ?></option>
                <?php endforeach; ?>
            </select>
            <small>Для выбора нескольких процедур удерживайте Ctrl</small>
        </div>

        <button type="submit" class="btn">Добавить врача</button>
    </form>
</div>
</body>
</html>