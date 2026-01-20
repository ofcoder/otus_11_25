<?php

use Otus\Orm\BookTable;
use Bitrix\Main\Data\Cache;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/**
 * @var CMain $APPLICATION
 */

$APPLICATION->SetTitle('Кэширование методами ядра d7');

$cacheTime = 30;
$cacheId = 'books_list';
$cacheDir = 'books';

$timestamp = time() + $cacheTime;
echo date('Время сброса кеша') . '<br>';
echo date('H.i.s', (int)$timestamp) . '<br>';

$cache = Cache::createInstance();
// $cache->cleanDir($cacheDir);
if ($cache->initCache($cacheTime, $cacheId, $cacheDir)) {
    echo 'Закешированные данные: <br>';
    $arResult = $cache->getVars();
} else {
    echo 'Данные получаются из БД: <br>';
    $arResult = BookTable::getList()->fetchAll();

    if ($cache->startDataCache()) {
        $cache->endDataCache($arResult);
    }
}

dump($arResult);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
