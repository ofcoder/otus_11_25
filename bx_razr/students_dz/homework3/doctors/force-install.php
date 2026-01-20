<?php
// /bx_razr/students_dz/homework3/doctors/force-install.php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Install\Doctors\CheckInstall;

echo "<h1>Принудительная переустановка приложения</h1>";

// Очищаем кэш
if (class_exists('\Bitrix\Main\Data\Cache')) {
    $cache = \Bitrix\Main\Data\Cache::createInstance();
    $cache->cleanDir('b_iblock');
    $cache->cleanDir('b_iblock_property');
    echo "<p>Кэш очищен</p>";
}

// Принудительная переустановка
echo "<p>Запуск принудительной переустановки...</p>";
$result = CheckInstall::forceReinstall();

if ($result) {
    echo "<p style='color: green;'>✅ Переустановка завершена успешно!</p>";
} else {
    echo "<p style='color: red;'>❌ Ошибка при переустановке</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>Вернуться в приложение</a></p>";
echo "<p><a href='index.php?action=install'>Переустановить через интерфейс</a></p>";

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');