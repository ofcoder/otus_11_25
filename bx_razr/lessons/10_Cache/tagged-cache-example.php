<?php

use Bitrix\Main\Application;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Engine\CurrentUser;
use Otus\Orm\BookTable;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/**
 * @var CMain $APPLICATION
*/

$APPLICATION->SetTitle('Тегированное кэширование d7');

$cache = Cache::createInstance();
$taggedCache = Application::getInstance()->getTaggedCache();

$cacheTime = 300;
$cacheId = 'books_tag_cache_' . CurrentUser::get()->getId();
$cacheDir = 'books';
$cacheTag = 'BOOK_LIST';

if ($cache->initCache($cacheTime, $cacheId, $cacheDir)) {
    echo 'Читаем из кэша:<br>';
    $bookList = $cache->getVars();
} else {
    $cache->startDataCache();
    $taggedCache->startTagCache($cacheDir);
    echo 'Пишем в кэш:<br>';

    $taggedCache->registerTag($cacheTag);

    $bookList = BookTable::getList()->fetchAll();

    $taggedCache->endTagCache();
    $cache->endDataCache($bookList);
}

dump($bookList);

//$taggedCache->clearByTag($cacheTag); /* = Application::getInstance()->getTaggedCache();*/

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
