<?php
if (php_sapi_name() != 'cli')
{
    die();
}

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_NO_ACCELERATOR_RESET", true);
define("BX_CRONTAB", true);
define("STOP_STATISTICS", true);
define("NO_AGENT_STATISTIC", "Y");
define("DisableEventsCheck", true);
define("NO_AGENT_CHECK", true);

$_SERVER['DOCUMENT_ROOT'] = realpath('/home/bitrix/www');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Entity\Base;
use Bitrix\Main\Application;
use Otus\Orm\BookTable;
use Otus\Orm\AuthorTable;

$entities = [
    AuthorTable::class,
    BookTable::class,
];

foreach ($entities as $entity) {
    // Попытка соединение с таблицей ORM сущности
    $entityTableConnection = Application::getConnection($entity::getConnectionName());
    if (!$entityTableConnection->isTableExists($entity::getTableName())) {
        $entityInstance = Base::getInstance($entity);
        $entityInstance->createDbTable();
    }
}

$connection = Application::getConnection();

$tableName = 'aholin_book_author';

if (!$connection->isTableExists($tableName)) {
    $connection->queryExecute("
		CREATE TABLE {$tableName} (
			BOOK_ID int NOT NULL,
			AUTHOR_ID int NOT NULL,
			PRIMARY KEY (BOOK_ID, AUTHOR_ID)
		)
	");
}
