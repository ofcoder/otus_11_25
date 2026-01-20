<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
use Diag\Logger;

$APPLICATION->SetTitle("Добавление в лог");
?>
    <ul class="list-group">
        <li class="list-group-item">
            <a href="/local/logs/debug.txt">Файл лога</a>,
            в лог добавленно 'Открыта страница writelog.php'
        </li>
    </ul>
<?php
Logger::dumpToFile(date('Y-m-d H:i:s'));
//require_once($_SERVER["DOCUMENT_ROOT"] . "/otus/debug.php");

?>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>