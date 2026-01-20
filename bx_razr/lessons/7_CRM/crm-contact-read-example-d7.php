<?php

use Bitrix\Crm\ContactTable;
use Bitrix\Main\Loader;

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php';

/**
 * @var \CMain $APPLICATION
 */

$APPLICATION->SetTitle('Пример чтения списка контактов D7');

Loader::includeModule('crm');

$order = [
    'ID' => 'DESC', # or ASC
];

$filter = [
    '>=DATE_CREATE' => (new DateTime())->format('1.m.Y'),
];

$group = [];

$nav = false;

$select = [
    'ID',
    'NAME',
    'LAST_NAME',
    'SECOND_NAME',
    'UF_CRM_CONTACT_TELEGRAM',
];

// DealTable, CompanyTable, LeadTable, QuoteTable
$rawContacts = ContactTable::getList([
    'filter' => $filter,
    'select' => $select,
    'order' => $order,
    'group' => $group,
    'limit' => null,
    'offset' => null,
]);

$contacts = [];
while ($contact = $rawContacts->fetch()) {
    $contacts[] = $contact;
}

dump($contacts);

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php';
