<?php
// /bx_razr/students_dz/homework3/doctors/test-d7.php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Controllers\TestController;

$testController = new TestController();
$testController->testD7ORM();

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');