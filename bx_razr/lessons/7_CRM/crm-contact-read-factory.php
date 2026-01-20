<?php

use Bitrix\Crm\Service\Container;
use Bitrix\Main\Loader;

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php';

/**
 * @var \CMain $APPLICATION
 */

$APPLICATION->SetTitle('Пример чтения списка контактов с помощью фабрики');

Loader::includeModule('crm');

$order = [
    'ID' => 'DESC', # or ASC
];

$filter = [
    '>=DATE_CREATE' => (new DateTime())->format('1.m.Y'),
];

$group = [];

$nav = false;

$select = [];

$crmServiceContainer = Container::getInstance();
/*
 *  public const Undefined = 0;
	public const Lead = 1;
	public const Deal = 2;
	public const Contact = 3;
	public const Company = 4;
	public const Invoice = 5;
	public const Activity = 6;
	public const Quote = 7;
	public const Requisite = 8;
	public const DealCategory = 9;
	public const CustomActivityType = 10;
	public const Wait = 11;
	public const CallList = 12;
	public const DealRecurring = 13;
	public const Order = 14;
	public const OrderCheck = 15;
	public const OrderShipment = 16;
	public const OrderPayment = 17;
 */
$contactFactory = $crmServiceContainer->getFactory(\CCrmOwnerType::Contact);

$contacts = $contactFactory->getItems([
    'filter' => $filter,
    'select' => $select,
    'order' => $order,
    'group' => $group,
    'limit' => null,
    'offset' => null,
]);

foreach ($contacts as $contact) {
    $company = $contact->getCompany();
    $contactFio = $contact->getFullName();
    echo sprintf('Компанию %s представляет менеджер %s%s', $company?->getTitle() ?? '', $contactFio, '<br>');
}

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php';
