<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
use Bitrix\Main\IO,
    Bitrix\Main\Application,
    Bitrix\Main\Config\Configuration;

$bitrixLogFile = Configuration::getValue('exception_handling')['log']['settings']['file'];
$path = Application::getDocumentRoot() . '/' . $bitrixLogFile;
IO\File::putFileContents($path, '');

LocalRedirect('/bx_razr/students_dz/homework2/');
