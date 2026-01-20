<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadblockTable;

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/**
 * @global \CMain $APPLICATION
 */

$APPLICATION->SetTitle('Пример чтения из hlblock-а');
Loader::includeModule('highloadblock');

$pantoneColorsHlInfo = HighloadblockTable::getList([
    'filter' => [
        'NAME' => 'PantoneColors',
    ],
])->fetch();

$entity = HighloadblockTable::compileEntity($pantoneColorsHlInfo);
$hlBlockEntityClassName = $entity->getDataClass();

// ИЛИ \PantoneColorsTable::getList();
$colors = $hlBlockEntityClassName::getList([
    'filter' => [
        '!UF_COLOR_PANTONE_CODE' => null,
    ],
])->fetchCollection();

foreach ($colors as $color) {
    echo 'Название цвета: ' . $color->getUfName() . ' -> ';
    echo 'Цвет <div style="display: inline-block;width:50px;height:50px;background-color:#' . $color->getUfColorHexCode() . '"></div><br>';
    echo 'Код цвета по Пантону: ' . $color->getUfColorPantoneCode() . '<br>';
}

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
