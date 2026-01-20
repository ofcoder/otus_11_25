<?php

return[
'exception_handling' => array (
      'value' =>
        array (
          'debug' => true,
          'handled_errors_types' => 4437,
          'exception_errors_types' => 4437,
          'ignore_silence' => false,
          'assertion_throws_exception' => true,
          'assertion_error_type' => 256,
          'log' => [
            'class_name' => '\\Diag\\FileExceptionHandlerLogOfcoder', // Название своего класса
// Система ищет или в папке bitrix, или в local, путь начинается после bitrix/ или local/
            'required_file' => 'App/Diag/FileExceptionHandlerLogOfcoder.php',
// Если БД недоступна, то хотя бы запишет в файл
            'settings' => [
              'file' => 'local/logs/FileExceptionHandlerLogOfcoder.txt',
              'log_size' => 1000000,
// Битрикс по умолчанию генерирует уйму, просто кучу исторических ошибок. Чтобы не забить, игнориуем один из типов
              'dont_show' => ['\Bitrix\Main\Diag\ExceptionHandlerLog::LOW_PRIORITY_ERROR']
            ],
          ],
        ),
      'readonly' => false,
    )
];

