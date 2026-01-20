<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadblockTable;

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/**
 * @global \CMain $APPLICATION
 */

$APPLICATION->SetTitle('Пример обновления элемента hlblock-а');
Loader::includeModule('highloadblock');

$pantoneColorsHlInfo = HighloadblockTable::getList([
    'filter' => [
        'NAME' => 'PantoneColors',
    ],
])->fetch();

$entity = HighloadblockTable::compileEntity($pantoneColorsHlInfo);
$hlBlockEntityClassName = $entity->getDataClass();

// ИЛИ \PantoneColorsTable::getList();
$updateResult = $hlBlockEntityClassName::update(3, [
    'UF_COLOR_PANTONE_CODE' => '11C',
    'UF_NAME' => 'Cool Gray',
    'UF_COLOR_HEX_CODE' => '505457',
    'UF_XML_ID' => 'some_code',
]);

if ($updateResult->isSuccess()) {
    echo 'Обновлен элемент с ID = ' . $updateResult->getId();
}

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
