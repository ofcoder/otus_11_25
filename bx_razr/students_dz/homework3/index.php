<?php
// /bx_razr/students_dz/homework3/doctors/project-files.php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application;

$projectFiles = [
    'Установщик (старое ядро)' => '/local/App/Install/Doctors/Installer.php',
    'Проверка установки' => '/local/App/Install/Doctors/CheckInstall.php',
    'Контроллер врачей (ORM D7)' => '/local/App/Controllers/DoctorsController.php',
    'Контроллер тестов' => '/local/App/Controllers/TestController.php',
    'Роутер (ORM D7)' => '/local/App/Routers/DoctorsRouter.php',
    'Модель врачей (ORM D7)' => '/local/App/Models/Lists/ElementDoctorsTable.php',
    'Модель процедур (ORM D7)' => '/local/App/Models/Lists/ElementProceduresTable.php',
    'Абстрактная модель' => '/local/App/Models/AbstractIblockEntity.php',
    'Точка входа' => '/bx_razr/students_dz/homework3/doctors/index.php',
    'Шаблон списка врачей' => '/local/App/Views/Doctors/doctors_list.php',
    'Шаблон редактирования врача (ORM D7)' => '/local/App/Views/Doctors/edit_doctor.php',
    'Шаблон деталей врача' => '/local/App/Views/Doctors/doctor_detail.php',
    'Шаблон добавления врача' => '/local/App/Views/Doctors/add_doctor.php',
    'Шаблон добавления процедуры' => '/local/App/Views/Doctors/add_procedure.php',
    'Шаблон установки' => '/local/App/Views/Doctors/install.php',
    'Шаблон проверки списков' => '/local/App/Views/Doctors/check_lists.php',
    'Шаблон ошибки' => '/local/App/Views/Doctors/error.php',
    'Стили' => '/local/App/Views/Doctors/assets/style.css',
];

echo '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Файлы проекта</title>';
echo '<style>';
echo 'body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }';
echo '.container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }';
echo 'h1 { color: #333; border-bottom: 2px solid #0066cc; padding-bottom: 10px; }';
echo '.file-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 15px; margin: 20px 0; }';
echo '.file-card { background: #f9f9f9; border: 1px solid #ddd; border-radius: 5px; padding: 15px; }';
echo '.file-card h3 { margin: 0 0 10px 0; color: #0066cc; font-size: 16px; }';
echo '.file-card p { margin: 5px 0; color: #666; font-size: 14px; }';
echo '.file-card .path { font-family: monospace; background: #f0f0f0; padding: 5px; border-radius: 3px; font-size: 12px; }';
echo '.file-card .tech { display: inline-block; background: #e0e0e0; padding: 2px 6px; border-radius: 3px; font-size: 11px; margin-top: 5px; }';
echo '.file-card .tech.orm { background: #d4edda; color: #155724; }';
echo '.file-card .tech.old { background: #f8d7da; color: #721c24; }';
echo '.file-card a { display: inline-block; background: #0066cc; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; margin-top: 10px; font-size: 14px; }';
echo '.file-card a:hover { background: #0052a3; }';
echo '.actions { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; }';
echo '.actions a { display: inline-block; margin-right: 15px; background: #28a745; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; }';
echo '.actions a:hover { background: #218838; }';
echo '.legend { background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 20px; }';
echo '.legend span { display: inline-block; margin-right: 15px; }';
echo '.legend .orm-badge, .legend .old-badge { padding: 2px 6px; border-radius: 3px; font-size: 12px; }';
echo '.legend .orm-badge { background: #d4edda; color: #155724; }';
echo '.legend .old-badge { background: #f8d7da; color: #721c24; }';
echo '</style>';
echo '</head><body>';

echo '<div class="container">';
echo '<h1>📁 Файлы проекта "Врачи и процедуры"</h1>';
echo '<div class="legend">';
echo '<span><strong>Технологии:</strong></span>';
echo '<span><span class="orm-badge">ORM D7</span> - используется в основном приложении</span>';
echo '<span><span class="old-badge">Старое ядро</span> - используется только в установщике</span>';
echo '</div>';
echo '<p>Нажмите "Редактировать", чтобы открыть файл в админке Битрикс.</p>';

echo '<div class="file-grid">';
foreach ($projectFiles as $title => $path) {
    $fullPath = htmlspecialchars($path);
    $adminLink = '/bitrix/admin/fileman_file_edit.php?path=' . urlencode($path) . '&full_src=Y&site=s1&lang=ru&filter=Y&set_filter=Y';
    $exists = file_exists($_SERVER['DOCUMENT_ROOT'] . $path);
    
    // Определяем технологию
    $tech = 'orm';
    $techClass = 'orm';
    $techText = 'ORM D7';
    
    if (strpos($path, 'Installer.php') !== false) {
        $tech = 'old';
        $techClass = 'old';
        $techText = 'Старое ядро';
    }
    
    echo '<div class="file-card">';
    echo '<h3>' . $title . '</h3>';
    echo '<div class="path">' . $fullPath . '</div>';
    echo '<div class="tech ' . $techClass . '">' . $techText . '</div>';
    if ($exists) {
        echo '<a href="' . $adminLink . '" target="_blank">📝 Редактировать в админке</a>';
    } else {
        echo '<span style="color: #dc3545; font-size: 12px;">❌ Файл не найден</span>';
    }
    echo '</div>';
}
echo '</div>';

echo '<div class="actions">';
echo '<a href="/bx_razr/students_dz/homework3/doctors/">🏠 Вернуться в приложение</a>';
echo '<a href="/bx_razr/students_dz/homework3/doctors/?action=install">🔧 Переустановить данные</a>';
echo '<a href="/bx_razr/students_dz/homework3/doctors/check-lists.php">📋 Проверить настройки</a>';
echo '</div>';

echo '</div>';
echo '</body></html>';

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');