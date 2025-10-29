<?php

$iblockDepartmentID = \Bitrix\Main\Config\Option::get('intranet', 'iblock_structure', false);
$entityDepartment = \Bitrix\Iblock\Model\Section::compileEntityByIblock($iblockDepartmentID);

$dbRes = $entityDepartment::query()
    ->setSelect([
        'ID',
        'UF_HEAD',
    ])
    ->where('NAME', '=', 'Отдел продаж')
    ->exec();

if ($res = $dbRes->fetch()) {
    print_r($res);
    if ($res['UF_HEAD']){
        $this->SetVariable('HEAD', $res['UF_HEAD']);
    }
}