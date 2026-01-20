<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle('Вывод связанных полей');

use Models\Lists\CarsPropertyValuesTable as CarsTable;

// получаем список записей из инфоблока Автомобили в виде массива
$cars = CarsTable::getList([       
		'select'=>[
          'ID'=>'IBLOCK_ELEMENT_ID',
          'NAME'=>'ELEMENT.NAME',
 		  'MANUFACTURER_ID'=>'MANUFACTURER_ID',
      ]
  ])->fetchAll();

 pr($cars); 

// получаем список записей из инфоблока Автомобили в виде массива методом query
/*$cars = CarsTable::query()
    ->setSelect([
        '*',
        'NAME' => 'ELEMENT.NAME',
        'MANUFACTURER_NAME' => 'MANUFACTURER.ELEMENT.NAME',
        'CITY_NAME' => 'CITY.ELEMENT.NAME',
        'COUNTRY' => 'MANUFACTURER.COUNTRY', 
    ])
    ->setOrder(['COUNTRY' => 'desc'])
    ->registerRuntimeField(
        null,
        new \Bitrix\Main\Entity\ReferenceField(
            'MANUFACTURER',
            \Models\Lists\CarManufacturerPropertyValuesTable::getEntity(),
            ['=this.MANUFACTURER_ID' => 'ref.IBLOCK_ELEMENT_ID']
        )
    )   
    ->fetchAll();
pr($cars);
*/


