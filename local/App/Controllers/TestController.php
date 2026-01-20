<?php
namespace Controllers;

use Models\Lists\ElementDoctorsTable;
use Models\Lists\ElementProceduresTable;

class TestController
{
    public function testD7ORM(): void
    {
        echo "<h1>Тест D7 ORM</h1>";
        
        try {
            // Тест 1: Получение списка врачей
            echo "<h2>Тест 1: Получение списка врачей</h2>";
            $doctors = ElementDoctorsTable::getDoctorsList();
            echo "<pre>";
            print_r($doctors);
            echo "</pre>";
            
            // Тест 2: Получение списка процедур
            echo "<h2>Тест 2: Получение списка процедур</h2>";
            $procedures = ElementProceduresTable::getAllProcedures();
            echo "<pre>";
            print_r($procedures);
            echo "</pre>";
            
            // Тест 3: Получение врачей с процедурами
            echo "<h2>Тест 3: Получение врачей с процедурами</h2>";
            $doctorsWithProcedures = ElementDoctorsTable::getDoctorsWithProcedures();
            echo "<pre>";
            print_r($doctorsWithProcedures);
            echo "</pre>";
            
            // Тест 4: Добавление процедуры
            echo "<h2>Тест 4: Добавление процедуры</h2>";
            $newProcedureId = ElementProceduresTable::addProcedure('Новая тестовая процедура');
            if ($newProcedureId) {
                echo "Добавлена процедура с ID: " . $newProcedureId . "<br>";
                $procedure = ElementProceduresTable::getProcedureById($newProcedureId);
                echo "Получена процедура: ";
                print_r($procedure);
            }
            
            // Тест 5: Добавление врача
            echo "<h2>Тест 5: Добавление врача</h2>";
            $newDoctorId = ElementDoctorsTable::addDoctor([
                'LAST_NAME' => 'Тестовый',
                'FIRST_NAME' => 'Врач',
                'SECOND_NAME' => 'Докторович',
                'PROCEDURES' => [$newProcedureId]
            ]);
            
            if ($newDoctorId) {
                echo "Добавлен врач с ID: " . $newDoctorId . "<br>";
                $doctor = ElementDoctorsTable::getDoctorById($newDoctorId);
                echo "Получен врач: ";
                print_r($doctor);
            }
            
        } catch (\Exception $e) {
            echo "<h2>Ошибка:</h2>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
    }
}