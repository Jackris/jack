<?php

$rootActivity = $this->GetRootActivity();
CModule::IncludeModule('crm');
$cid = '{=Variable:we}';

$req = new \Bitrix\Crm\EntityRequisite();
$rs = $req->getList([
    "filter" => [
        "ENTITY_ID" => $cid,
        "ENTITY_TYPE_ID" => CCrmOwnerType::Company,
    ],
]);

$rows = $rs->fetchAll();
$c_c = count($rows);
$allcompanies = serialize($rows);

$i = 1;
$str = "";
foreach ($rows as $row) {
    $str = $str . $i . ") " . $row["NAME"] . ": " . $row["RQ_COMPANY_NAME"] . ", ИНН " . $row["RQ_INN"] . "\n";
    $i++;
}

$rootActivity->SetVariable("comp_count", $c_c);
$rootActivity->SetVariable("companies_arr", $allcompanies);
$rootActivity->SetVariable("companies", $str);
