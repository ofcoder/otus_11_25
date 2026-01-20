<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadblockTable;

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/**
 * @global \CMain $APPLICATION
 */

$APPLICATION->SetTitle('Пример создания элемента hlblock-а');
Loader::includeModule('highloadblock');

$pantoneColorsHlInfo = HighloadblockTable::getList([
    'filter' => [
        'NAME' => 'PantoneColors',
    ],
])->fetch();

$entity = HighloadblockTable::compileEntity($pantoneColorsHlInfo);
$hlBlockEntityClassName = $entity->getDataClass();

// ИЛИ \PantoneColorsTable::getList();
$addResult = $hlBlockEntityClassName::add([
    'UF_COLOR_PANTONE_CODE' => '32klkjldsajmlk',
    'UF_NAME' => 'Серый',
    'UF_COLOR_HEX_CODE' => '888888',
    'UF_XML_ID' => 'some_code',
]);

if ($addResult->isSuccess()) {
    echo 'Создан элемент с ID = ' . $addResult->getId();
}

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
