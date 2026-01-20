<?php
// /bx_razr/students_dz/homework3/doctors/test-orm.php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Models\Lists\ElementDoctorsTable;
use Models\Lists\ElementProceduresTable;
use Bitrix\Main\Loader;
Loader::includeModule('iblock');

echo "<h1>Тестирование D7 ORM</h1>";

try {
    // Тест 1: Проверка получения DataClass
    echo "<h2>Тест 1: Получение DataClass</h2>";
    
    $doctorsDataClass = ElementDoctorsTable::getDataClass();
    echo "<p>DataClass врачей: " . $doctorsDataClass . "</p>";
    
    $proceduresDataClass = ElementProceduresTable::getDataClass();
    echo "<p>DataClass процедур: " . $proceduresDataClass . "</p>";
    
    // Тест 2: Получение списка процедур
    echo "<h2>Тест 2: Получение списка процедур</h2>";
    $procedures = ElementProceduresTable::getAllProcedures();
    echo "<pre>";
    print_r($procedures);
    echo "</pre>";
    
    // Тест 3: Получение списка врачей
    echo "<h2>Тест 3: Получение списка врачей</h2>";
    $doctors = ElementDoctorsTable::getDoctorsList();
    echo "<pre>";
    print_r($doctors);
    echo "</pre>";
    
    // Тест 4: Получение врачей с процедурами
    echo "<h2>Тест 4: Получение врачей с процедурами</h2>";
    $doctorsWithProcedures = ElementDoctorsTable::getDoctorsWithProcedures();
    echo "<pre>";
    print_r($doctorsWithProcedures);
    echo "</pre>";
    
    // Тест 5: Добавление новой процедуры
    echo "<h2>Тест 5: Добавление новой процедуры</h2>";
    $newProcedureId = ElementProceduresTable::addProcedure('Новая тестовая процедура');
    if ($newProcedureId) {
        echo "<p>✅ Процедура добавлена с ID: $newProcedureId</p>";
        
        // Получаем добавленную процедуру
        $procedure = ElementProceduresTable::getProcedureById($newProcedureId);
        echo "<pre>";
        print_r($procedure);
        echo "</pre>";
    } else {
        echo "<p>❌ Не удалось добавить процедуру</p>";
    }
    
    // Тест 6: Получение врача по id
    echo "<h2>Тест 7: Получение врача по id</h2>";
    $doctor = ElementDoctorsTable::getDoctorById($doctorsWithProcedures[0]['ID']);
    echo "<pre>";
    print_r($doctor);
    echo "</pre>";
    
    // Тест 7: Добавление нового врача
    echo "<h2>Тест 6: Добавление нового врача</h2>";
    $newDoctorId = null;
    $proceduresClass = Bitrix\Iblock\IblockTable::compileEntity('Procedures')->getDataClass();
    $doctorsClass = Bitrix\Iblock\IblockTable::compileEntity('Doctors')->getDataClass();

// Находим процедуры для привязки
    $relatedProcedures = $proceduresClass::query()
        ->setSelect(['*'])
        ->whereIn('ID', [$newProcedureId])
        ->fetchCollection();
 
    $doctor = $doctorsClass::createObject()
        ->setName('Прохоров Игорь Михайлович')
        ->setLastName('Прохоров')
        ->setFirstName('Игорь')
        ->setSecondName('Михайлович');
    //Сначала сохраняем доктора
    $doctor->save();
    // а потом привязываем найденные процедуры
    foreach ($relatedProcedures as $procedure) {
        $doctor->addTo('PROCEDURES', $procedure->getId());
    }
    
    
    
    
    
    if ($doctor) {
        echo "<p>✅ Врач добавлен с ID:" . $doctor->getId() ."</p>";
        
        // Получаем добавленного врача
        $doctorData = ElementDoctorsTable::getDoctorById($doctor->getId());
        echo "<pre>";
        var_dump($doctorData);
        echo "</pre>";
    } else {
        echo "<p>❌ Не удалось добавить врача</p>";
    }
    

    
} catch (\Exception $e) {
    echo "<h2 style='color: red;'>Ошибка:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<hr>";
echo "<p><a href='index.php'>Вернуться в приложение</a></p>";

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');