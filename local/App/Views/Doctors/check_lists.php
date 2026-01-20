<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="<?= $assetsUrl ?>style.css">
    <style>
        .status-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .status-table th, .status-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .status-table th {
            background-color: #f4f4f4;
        }
        .status-ok { color: green; }
        .status-warning { color: orange; }
        .status-error { color: red; }
        .status-icon { font-weight: bold; margin-right: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Проверка настроек универсальных списков</h1>
            <nav>
                <a href="<?= $baseUrl ?>" class="btn btn-primary">Назад к списку</a>
                <a href="<?= $baseUrl ?>?action=install" class="btn">Переустановить</a>
                <a href="<?= $baseUrl ?>check-lists.php" class="btn">Подробная проверка</a>
            </nav>
        </header>
        
        <div class="check-results">
            <?php foreach ($settings as $code => $info): ?>
                <h2>Список: <?= htmlspecialchars($code) ?></h2>
                
                <table class="status-table">
                    <tr>
                        <th>Параметр</th>
                        <th>Статус</th>
                        <th>Значение</th>
                    </tr>
                    
                    <tr>
                        <td>Существование</td>
                        <td>
                            <?php if ($info['exists']): ?>
                                <span class="status-ok"><span class="status-icon">✅</span> Создан</span>
                            <?php else: ?>
                                <span class="status-error"><span class="status-icon">❌</span> Не найден</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($info['exists']): ?>
                                ID: <?= $info['id'] ?>, Название: <?= htmlspecialchars($info['name']) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>API_CODE</td>
                        <td>
                            <?php if ($info['has_api_code']): ?>
                                <span class="status-ok"><span class="status-icon">✅</span> Задан</span>
                            <?php else: ?>
                                <span class="status-error"><span class="status-icon">❌</span> Не задан</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $info['has_api_code'] ? 'Есть' : 'Отсутствует (критично)' ?></td>
                    </tr>
                    
                    <tr>
                        <td>XML_ID</td>
                        <td>
                            <?php if ($info['has_xml_id']): ?>
                                <span class="status-ok"><span class="status-icon">✅</span> Задан</span>
                            <?php else: ?>
                                <span class="status-error"><span class="status-icon">❌</span> Не задан</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $info['has_xml_id'] ? 'Есть' : 'Отсутствует (критично)' ?></td>
                    </tr>
                    
                    <tr>
                        <td>Режим списка</td>
                        <td>
                            <?php if ($info['list_mode'] === 'C'): ?>
                                <span class="status-ok"><span class="status-icon">✅</span> Правильный</span>
                            <?php else: ?>
                                <span class="status-warning"><span class="status-icon">⚠️</span> Неправильный</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($info['list_mode'] ?? '(не задан)') ?> (рекомендуется: C)</td>
                    </tr>
                    
                    <tr>
                        <td>REST API</td>
                        <td>
                            <?php if ($info['rest_enabled']): ?>
                                <span class="status-ok"><span class="status-icon">✅</span> Включен</span>
                            <?php else: ?>
                                <span class="status-warning"><span class="status-icon">⚠️</span> Выключен</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $info['rest_enabled'] ? 'Да' : 'Нет (рекомендуется включить)' ?></td>
                    </tr>
                    
                    <tr>
                        <td><strong>Общий статус</strong></td>
                        <td colspan="2">
                            <?php if ($info['fully_configured']): ?>
                                <span class="status-ok"><span class="status-icon">✅</span> Полностью настроен</span>
                                <p>Список готов к использованию в пользовательском интерфейсе</p>
                            <?php elseif ($info['exists']): ?>
                                <span class="status-warning"><span class="status-icon">⚠️</span> Требуется донастройка</span>
                                <p>Некоторые параметры не настроены корректно</p>
                            <?php else: ?>
                                <span class="status-error"><span class="status-icon">❌</span> Не создан</span>
                                <p>Требуется полная установка</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                
                <hr>
            <?php endforeach; ?>
            
            <div class="actions">
                <a href="<?= $baseUrl ?>?action=install" class="btn">Переустановить все списки</a>
                <a href="<?= $baseUrl ?>check-lists.php" class="btn">Подробная диагностика</a>
                <a href="<?= $baseUrl ?>" class="btn btn-primary">Вернуться в приложение</a>
            </div>
        </div>
    </div>
</body>
</html>