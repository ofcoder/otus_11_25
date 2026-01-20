<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Ошибка для exeption");
use Bitrix\Main\Config\Configuration;

$bitrixLogFile = Configuration::getValue('exception_handling')['log']['settings']['file'];
?>
<ul class="list-group">
    <li class="list-group-item">
        <a href="/<?=$bitrixLogFile?>">Файл лога</a>
    </li>
</ul>
<?php
$a = 1/0;
?>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
