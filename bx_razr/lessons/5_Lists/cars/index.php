<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle('Вывод связанных полей');

use Bitrix\Main\Loader;
use Bitrix\Iblock\Iblock;
Loader::includeModule('iblock');

$iblockId = 16;
$iblockElementId = 29;

// Old API 
$arFilter = ['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y'];
$arSelect = ['ID', 'NAME', 'CODE', 'PROPERTY_MODEL'];
$res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
while($arFields = $res->fetch()){
    pr($arFields);
}


/*// создание новой записи в инфоблоке через CIBlockElement
$arElementProps = [
    'MODEL' => 'X5',
];
$arIblockFields = [
    'IBLOCK_ID' => $iblockId,
    'NAME' => 'New element',
    'PROPERTY_VALUES' => $arElementProps
];
$objIblockElement = new \CIBlockElement();
$objIblockElement->Add($arIblockFields);*/


// ORM

// ORM с использованием wakeUp
// wakeUp - метод позволяет сохранить в переменную элемент инфоблока с которым можно работать как с объектом ORM

// с параметром получает IBLOCK_ID
//get by id 
/*$iblock = Iblock::wakeUp($iblockId);
$element = $iblock->getEntityDataClass()::getByPrimary(  // get props
	$iblockElementId, 
	['select' => ['NAME', 'MODEL']])
->fetchObject();

$name = $element->get('NAME');
echo 'NAME: ';
pr($name);

$model = $element->get('MODEL')->getValue();
echo 'MODEL: ';
pr($model);
*/


// ORM с использованием Element{код инфоблока}Table
// Element{код инфоблока}Table - метод позволяет сохранить в переменную коллекцию объектов (элементов инфоблока)
// get list
/*$elements = \Bitrix\Iblock\Elements\ElementCarTable::getList([ // car - cимвольный код API инфоблока
    'select' => ['MODEL'], // имя свойства 
])->fetchCollection();

foreach ($elements as $element) {
    pr('MODEL - '.$element->getModel()->getValue()); // получение значения свойства MODEL
}
*/

// query - метод позволяет строить более гибкие и сложные запросы для выборки данных данных через ORM
// получение через метод query списка элементов
/*$elements = \Bitrix\Iblock\Elements\ElementCarTable::query() // car - cимвольный код API инфоблока
    ->addSelect('NAME')
    ->addSelect('MODEL') // имя свойства 
    ->addSelect('ID')
    ->fetchCollection();

foreach ($elements as $key => $item) {
    pr($item->getName().' '.$item->getModel()->getValue()); // получение значения свойства MODEL
    $value = $item->getModel()->getValue();
    
    if($value == 'Q7'){
            $item->setModel('Q7 TEST'); // изменение значения свойства MODEL
            $item->save(); // сохранение данных
    }
}*/


// Получить свойства инфоблока
/*$dbIblockProps = \Bitrix\Iblock\PropertyTable::getList(array(
    'select' => array('*'),
    'filter' => array('IBLOCK_ID' =>$iblockId)
));
while ($arIblockProps = $dbIblockProps->fetch()){ 
    pr($arIblockProps);
}
*/


// Получить список элементов инфоблока
/*$dbItems = \Bitrix\Iblock\ElementTable::getList(array(
    'select' => array('ID', 'NAME', 'IBLOCK_ID'),
    'filter' => array('IBLOCK_ID' => $iblockId)
));
$items = [];
while ($arItem = $dbItems->fetch()){  
    $dbProperty = \CIBlockElement::getProperty(
        $arItem['IBLOCK_ID'],
        $arItem['ID']
    );
    while($arProperty = $dbProperty->Fetch()){  
        $arItem['PROPERTIES'][] = $arProperty;
    }
    $items [] = $arItem;
}
pr($items);
*/

// редактирование записей в БД
/*\Bitrix\Main\Loader::IncludeModule("iblock");
// делаем запрос на изменение поля NAME в записи (BMW X5) с ID 29
$res = \Bitrix\Iblock\Elements\ElementcarTable::update(29, array(
    'NAME' => 'TEST 777',
));
*/

// удаление записи из БД
/*$res = \Bitrix\Iblock\Elements\ElementcarTable::delete(95);
pr($res);
*/