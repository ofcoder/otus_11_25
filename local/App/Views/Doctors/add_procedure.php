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
        <h1>Добавить процедуру</h1>
        <nav>
            <a href="<?= $baseUrl ?>" class="btn btn-primary">Назад к списку</a>
            <a href="<?= $baseUrl ?>?action=add_doctor" class="btn">Добавить врача</a>
        </nav>
    </header>

    <form method="POST" class="add-form">
        <div class="form-group">
            <label for="name">Название процедуры:</label>
            <input type="text" id="name" name="name" required>
        </div>

        <button type="submit" class="btn">Добавить процедуру</button>
    </form>
</div>
</body>
</html>