<?php

use Bitrix\Main\Loader;
use Bitrix\Crm\Service\Container;

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php';

/**
 * @global \CMain $APPLICATION
 */

$APPLICATION->SetTitle('Создание сделки через фабрику и объект операции');

if (!Loader::includeModule('crm')) {
    return;
}

$dealFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);
// $newDealItem хранит в себе информацию о ещё не созданной сделке
$newDealItem = $dealFactory->createItem([
    'TITLE' => 'Тестовая сделка через Фабрику',
]);

//$newDealItem->set('TITLE', 'Тестовая сделка через Фабрику');
# $newDealItem->save(); Выполнит сохранение сразу без проверки в БД (не заполнит недостающие поля самостоятельно)
# без проверки прав доступа и без запуска обработчиков событий

$dealAddOperation = $dealFactory->getAddOperation($newDealItem);
$addResult = $dealAddOperation->launch();
echo sprintf('Создана сделка под названием "%s" с ИД = %d', $newDealItem->getTitle(), $newDealItem->getId());

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php';
