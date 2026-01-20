<?php
namespace Routers;

use Controllers\DoctorsController;
use Install\Doctors\CheckInstall;

class DoctorsRouter
{
    public static function handle(): void
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // Обработка маршрута /bx_razr/students_dz/homework3/doctors/
        if (preg_match('#^/bx_razr/students_dz/homework3/doctors/(.*)$#', $requestUri, $matches)) {
            $action = $_GET['action'] ?? 'index';
            $doctorId = (int)($_GET['doctor_id'] ?? 0);
            
            $controller = new DoctorsController();
            
            try {
                // Для всех действий кроме install проверяем инфоблоки
                if ($action !== 'install') {
                    CheckInstall::checkAndInstall();
                }
                
                switch ($action) {
                    case 'view':
                        $data = $controller->view($doctorId);
                        break;
                    case 'edit':
                        $data = $controller->editDoctor($doctorId, $_POST);
                        break;
                    case 'add_doctor':
                        $data = $controller->addDoctor($_POST);
                        break;
                    case 'add_procedure':
                        $data = $controller->addProcedure($_POST);
                        break;
                    case 'delete':
                        $data = $controller->deleteDoctor($doctorId);
                        break;
                    case 'install':
                        $data = $controller->install();
                        break;
                    case 'check_lists':
                        $data = $controller->checkLists();
                        break;
                    case 'index':
                    default:
                        $data = $controller->index();
                        break;
                }
                
                self::render($data['template'], $data['data']);
            } catch (\Exception $e) {
                self::render('error', [
                    'error' => 'Ошибка приложения: ' . $e->getMessage(),
                    'baseUrl' => '/bx_razr/students_dz/homework3/doctors/',
                    'assetsUrl' => '/local/App/Views/Doctors/assets/'
                ]);
            }
        }
    }
    
    private static function render(string $template, array $data = []): void
    {
        extract($data);
        $templatePath = $_SERVER['DOCUMENT_ROOT'] . '/local/App/Views/Doctors/' . $template . '.php';
        
        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            echo "Шаблон не найден: " . $template;
        }
    }
}