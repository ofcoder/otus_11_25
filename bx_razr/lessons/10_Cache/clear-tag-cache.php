<?php

use Bitrix\Main\Application;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/**
 * @var CMain $APPLICATION
*/

$APPLICATION->SetTitle('Очистка тегированного кэша d7');

$taggedCache = Application::getInstance()->getTaggedCache();

$cacheTag = 'BOOK_LIST';

$taggedCache->clearByTag($cacheTag); /* = Application::getInstance()->getTaggedCache();*/

echo 'Кэш с тегом ' . $cacheTag . ' очищен';

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
