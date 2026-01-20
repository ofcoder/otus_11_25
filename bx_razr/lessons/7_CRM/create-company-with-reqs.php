<?php
use Bitrix\Crm\Controller\Requisite\Entity as RequisiteController;
use Bitrix\Crm\Service\Container;
use Bitrix\Main\Loader;
use Bitrix\Crm\EntityRequisite;

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php';

/**
 * @global \CMain $APPLICATION
 */

$APPLICATION->SetTitle('Создание компании с заполненными реквизитами');

Loader::includeModule('crm');

$companyFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Company);

$companyItem = $companyFactory->createItem(['ИП Холин Александр Владимирович']);

$addOperation = $companyFactory->getAddOperation($companyItem);
$addOperation->launch();

$newCompanyId = $companyItem->getId();

$reqs = RequisiteController::searchAction(
    '771618967942',
    ['presetId' => 2], # 2 - так как ИП см. в таблицу b_crm_preset
);

$reqs = $reqs[0]['fields'];

$reqs['NAME'] = $companyItem->getTitle() . ' - реквизиты';
$reqs['ENTITY_ID'] = $newCompanyId;
$reqs['ENTITY_TYPE_ID'] = \CCrmOwnerType::Company;

(new EntityRequisite())->add($reqs);

dump($companyItem->getData());
dump($reqs);

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php';
