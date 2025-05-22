<?php

$rootActivity = $this->GetRootActivity();
CModule::IncludeModule('crm');
$compnum = '{=Variable:compnum}';
$cid = '{=Variable:we}';

$req = new \Bitrix\Crm\EntityRequisite();
$rs = $req->getList([
    "filter" => [
        "ENTITY_ID" => $cid,
        "ENTITY_TYPE_ID" => CCrmOwnerType::Company,
    ],
]);

$rows = $rs->fetchAll();

$params = [
    'filter' => [
        'ENTITY_ID' => $rows[$compnum - 1]['ID'],
        'ENTITY_TYPE_ID' => CCrmOwnerType::Requisite,
    ],
];
$bankReq = (new \Bitrix\Crm\EntityBankDetail)->getList($params)->fetchAll();

$i = 1;
$accounts = "";
$accounts_arr = serialize($bankReq);
$account_count = count($bankReq);

foreach ($bankReq as $br) {
    $accounts = $accounts . $i . ") " . $br["RQ_BANK_NAME"] . " в " . $br["RQ_BANK_ADDR"] . " БИК " . $br["RQ_BIK"] . " р/с " . $br["RQ_ACC_NUM"] . "\n";
    $i++;
}

$rootActivity->SetVariable("accounts", $accounts);
$rootActivity->SetVariable("accounts_arr", $accounts_arr);
$rootActivity->SetVariable("acc_count", $account_count);