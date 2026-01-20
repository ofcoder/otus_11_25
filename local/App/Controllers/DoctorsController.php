<?php
namespace Controllers;

use Models\Lists\ElementDoctorsTable;
use Models\Lists\ElementProceduresTable;
use Install\Doctors\Installer;
use Install\Doctors\CheckInstall;

class DoctorsController
{
    private $baseUrl = '/bx_razr/students_dz/homework3/doctors/';
    private $assetsUrl = '/local/App/Views/Doctors/assets/';
    
    public function index(): array
    {
        try {
            // Проверяем инфоблоки перед загрузкой данных
            $iblockExist = CheckInstall::checkIblocksExist();
            if (!$iblockExist) {
                // Автоматически устанавливаем инфоблоки
                CheckInstall::checkAndInstall();
            }
            
            $doctors = ElementDoctorsTable::getDoctorsWithProcedures();
            
            return [
                'template' => 'doctors_list',
                'data' => [
                    'doctors' => $doctors,
                    'title' => 'Список врачей',
                    'baseUrl' => $this->baseUrl,
                    'assetsUrl' => $this->assetsUrl
                ]
            ];
        } catch (\Exception $e) {
            return $this->getErrorTemplate('Ошибка при загрузке списка врачей: ' . $e->getMessage());
        }
    }
    
    public function install(): array
    {
        try {
            // Принудительная переустановка
            $result = CheckInstall::forceReinstall();
            
            if ($result) {
                // После установки получаем актуальный результат
                $installResult = Installer::install();
                $result = $installResult['success'];
            } else {
                $installResult = ['success' => false, 'errors' => ['Не удалось выполнить установку']];
            }
            
            return [
                'template' => 'install',
                'data' => [
                    'result' => $installResult,
                    'title' => 'Установка',
                    'baseUrl' => $this->baseUrl,
                    'assetsUrl' => $this->assetsUrl
                ]
            ];
        } catch (\Exception $e) {
            return $this->getErrorTemplate('Ошибка установки: ' . $e->getMessage());
        }
    }
    
    public function view(int $doctorId): array
    {
        try {
            $doctor = ElementDoctorsTable::getDoctorById($doctorId);
            
            if (!$doctor) {
                return $this->getErrorTemplate('Врач не найден');
            }
            
            return [
                'template' => 'doctor_detail',
                'data' => [
                    'doctor' => $doctor,
                    'title' => 'Процедуры врача',
                    'baseUrl' => $this->baseUrl,
                    'assetsUrl' => $this->assetsUrl
                ]
            ];
        } catch (\Exception $e) {
            return $this->getErrorTemplate('Ошибка при загрузке данных врача: ' . $e->getMessage());
        }
    }
    
    public function editDoctor(int $doctorId, array $data = []): array
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $success = ElementDoctorsTable::updateDoctor($doctorId, [
                    'LAST_NAME' => $data['last_name'] ?? '',
                    'FIRST_NAME' => $data['first_name'] ?? '',
                    'SECOND_NAME' => $data['second_name'] ?? '',
                    'PROCEDURES' => $data['procedures'] ?? []
                ]);
                
                if ($success) {
                    header('Location: ' . $this->baseUrl . '?action=view&doctor_id=' . $doctorId);
                    exit;
                } else {
                    return $this->getErrorTemplate('Не удалось обновить данные врача');
                }
            }
            
            // Получаем текущие данные врача
            $doctor = ElementDoctorsTable::getDoctorById($doctorId);
            if (!$doctor) {
                return $this->getErrorTemplate('Врач не найден');
            }
            
            $procedures = ElementProceduresTable::getAllProcedures();
            
            return [
                'template' => 'edit_doctor',
                'data' => [
                    'doctor' => $doctor,
                    'procedures' => $procedures,
                    'title' => 'Редактировать врача: ' . $doctor['FULL_NAME'],
                    'baseUrl' => $this->baseUrl,
                    'assetsUrl' => $this->assetsUrl
                ]
            ];
        } catch (\Exception $e) {
            return $this->getErrorTemplate('Ошибка при редактировании врача: ' . $e->getMessage());
        }
    }
    
    public function addDoctor(array $data): array
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $doctorId = ElementDoctorsTable::addDoctor([
                    'LAST_NAME' => $data['last_name'] ?? '',
                    'FIRST_NAME' => $data['first_name'] ?? '',
                    'SECOND_NAME' => $data['second_name'] ?? '',
                    'PROCEDURES' => $data['procedures'] ?? []
                ]);
                
                if ($doctorId) {
                    header('Location: ' . $this->baseUrl);
                    exit;
                } else {
                    return $this->getErrorTemplate('Не удалось добавить врача');
                }
            }
            
            $procedures = ElementProceduresTable::getAllProcedures();
            
            return [
                'template' => 'add_doctor',
                'data' => [
                    'procedures' => $procedures,
                    'title' => 'Добавить врача',
                    'baseUrl' => $this->baseUrl,
                    'assetsUrl' => $this->assetsUrl
                ]
            ];
        } catch (\Exception $e) {
            return $this->getErrorTemplate('Ошибка при добавлении врача: ' . $e->getMessage());
        }
    }
    
    public function addProcedure(array $data): array
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $procedureId = ElementProceduresTable::addProcedure($data['name'] ?? '');
                
                if ($procedureId) {
                    header('Location: ' . $this->baseUrl . '?action=index');
                    exit;
                } else {
                    return $this->getErrorTemplate('Не удалось добавить процедуру');
                }
            }
            
            return [
                'template' => 'add_procedure',
                'data' => [
                    'title' => 'Добавить процедуру',
                    'baseUrl' => $this->baseUrl,
                    'assetsUrl' => $this->assetsUrl
                ]
            ];
        } catch (\Exception $e) {
            return $this->getErrorTemplate('Ошибка при добавлении процедуры: ' . $e->getMessage());
        }
    }
    
    public function deleteDoctor(int $doctorId): array
    {
        try {
            if (ElementDoctorsTable::deleteDoctor($doctorId)) {
                header('Location: ' . $this->baseUrl);
                exit;
            } else {
                return $this->getErrorTemplate('Не удалось удалить врача');
            }
        } catch (\Exception $e) {
            return $this->getErrorTemplate('Ошибка при удалении врача: ' . $e->getMessage());
        }
    }
    
    public function checkLists(): array
    {
        try {
            $settings = CheckInstall::checkIblocksSettings();
            
            return [
                'template' => 'check_lists',
                'data' => [
                    'settings' => $settings,
                    'title' => 'Проверка настроек списков',
                    'baseUrl' => $this->baseUrl,
                    'assetsUrl' => $this->assetsUrl
                ]
            ];
        } catch (\Exception $e) {
            return $this->getErrorTemplate('Ошибка при проверке настроек: ' . $e->getMessage());
        }
    }
    
    private function getErrorTemplate(string $error): array
    {
        return [
            'template' => 'error',
            'data' => [
                'error' => $error,
                'baseUrl' => $this->baseUrl,
                'assetsUrl' => $this->assetsUrl
            ]
        ];
    }
}