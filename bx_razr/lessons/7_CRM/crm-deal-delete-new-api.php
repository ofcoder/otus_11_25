<?php

use Bitrix\Main\Loader;
use Bitrix\Crm\Service\Container;

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php';

/**
 * @global \CMain $APPLICATION
 */

$APPLICATION->SetTitle('Удаление сделки через фабрику и объект операции');

if (!Loader::includeModule('crm')) {
    return;
}

$dealId = 12;
$dealFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);
// $newDealItem хранит в себе информацию о ещё не созданной сделке
$newDealItem = $dealFactory->getItem($dealId);
#$newDealItem->delete(); быстро но работает как delete в MySQL
$dealDeleteOperation = $dealFactory->getDeleteOperation($newDealItem);
$deleteResult = $dealDeleteOperation->launch();
dump($deleteResult);
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php';
