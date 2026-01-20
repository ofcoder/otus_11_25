<?php
// Подключаем ядро Битрикс
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
//require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

// Подключаем наше приложение
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/App/Routers/DoctorsRouter.php');

use Routers\DoctorsRouter;

// Запускаем роутер
DoctorsRouter::handle();

// Подключаем эпилог
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');
//require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');