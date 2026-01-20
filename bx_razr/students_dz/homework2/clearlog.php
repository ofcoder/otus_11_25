<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

use Bitrix\Main\IO,
    Bitrix\Main\Application;

$path = Application::getDocumentRoot() . "/local/logs/debug.txt";
//$file = new IO\File($path);
//$file->delete();
IO\File::putFileContents($path, '');

LocalRedirect('/bx_razr/students_dz/homework2/');
