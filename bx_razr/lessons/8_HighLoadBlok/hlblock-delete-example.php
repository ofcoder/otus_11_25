<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadblockTable;

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/**
 * @global \CMain $APPLICATION
 */

$APPLICATION->SetTitle('Пример удаления элемента hlblock-а');
Loader::includeModule('highloadblock');

$pantoneColorsHlInfo = HighloadblockTable::getList([
    'filter' => [
        'NAME' => 'PantoneColors',
    ],
])->fetch();

$entity = HighloadblockTable::compileEntity($pantoneColorsHlInfo);
$hlBlockEntityClassName = $entity->getDataClass();

$hlBlockEntityClassName::delete(1);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
