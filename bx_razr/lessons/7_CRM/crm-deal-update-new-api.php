<?php

use Bitrix\Main\Loader;
use Bitrix\Crm\Service\Container;

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php';

/**
 * @global \CMain $APPLICATION
 */

$APPLICATION->SetTitle('Изменение сделки через фабрику и объект операции');

if (!Loader::includeModule('crm')) {
    return;
}

$dealId = 13;
$dealFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);
$newDealItems = $dealFactory->getItems([
    'filter' => [
        '=ID' => $dealId
    ],
    'select' => [
        'ID', 'TITLE',
    ],
]);
// Если select ограничен набором полей, то не стоит переживать за то что остальные поля при сохранении обнулятся
foreach ($newDealItems as $dealItem) {
    $dealItem->set('TITLE', sprintf('OTUS - %s', $dealItem->getTitle()));
    $dealItem->save();
}

//throw new \Bitrix\Main\SystemException('ВАМ НЕЛЬЗЯ МЕНЯТЬ СДЕЛКУ');
//$dealUpdateOperation = $dealFactory->getUpdateOperation($newDealItem);
//$addResult = $dealUpdateOperation->launch();
//echo sprintf('Изменена сделка под названием "%s" с ИД = %d', $newDealItem->getTitle(), $newDealItem->getId());

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php';
