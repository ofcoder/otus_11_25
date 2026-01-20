<?php

use Bitrix\Main\Loader;

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php';

/**
 * @var \CMain $APPLICATION
 */

$APPLICATION->SetTitle('Пример чтения списка контактов Старое ядро');

Loader::includeModule('crm');

$order = [
    'ID' => 'DESC', # or ASC
];

$filter = [
    '>=DATE_CREATE' => (new DateTime())->format('1.m.Y'),
];

$group = false;

$nav = false;

$select = [
    'ID',
    'NAME',
    'LAST_NAME',
    'SECOND_NAME',
    'UF_CRM_CONTACT_TELEGRAM',
];

// CCrmDeal,CCrmLead,CCrmCompany,CCrmQuote

$rawContacts = \CCrmContact::GetListEx($order, $filter, $group, $nav, $select);

$contacts = [];
while ($contact = $rawContacts->Fetch()) {
    $contacts[] = $contact;
}

dump($contacts);

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php';
