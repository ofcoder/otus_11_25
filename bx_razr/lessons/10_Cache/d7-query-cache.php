<?php

use Otus\Orm\BookTable;
use Bitrix\Main\Application;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/**
 * @var CMain $APPLICATION
 */

$APPLICATION->SetTitle('Кеш запросов d7');

$connection = Application::getConnection();
$connection->startTracker();
$books = BookTable::getList([
    'cache' => [
        'ttl' => 60,
        'cache_joins' => true,
    ],
])->fetchAll();
$connection->stopTracker();

// BookTable::cleanCache();

$tracker = $connection->getTracker();

dump($tracker->getQueries());

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
