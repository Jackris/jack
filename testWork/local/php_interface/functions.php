<?php

function addDealCheckAgent(){
    $daysPeriod = 1;
    $daysInSeconds = 86400 * $daysPeriod;

    $date = date('d.m.Y') . ' 10:00:00';
    $date = date('d.m.Y H:i:s', strtotime($date . '+' . $daysPeriod . ' day'));

    $date = date('d.m.Y') . ' 20:51:00';

    \CAgent::AddAgent(
        '\Agents::checkDeals();',
        '',
        'N',
        $daysInSeconds,
        '',
        'Y',
        $date
    );
}

function addExpiredStage(){
    if (!\Bitrix\Main\Loader::includeModule('crm')) {
        return false;
    }
    $result = Bitrix\Crm\StatusTable::add([
        'ENTITY_ID' => 'DEAL_STAGE',
        'STATUS_ID' => 'EXPIRED',
        'NAME'      => 'Просрочено',
        'SORT'      => 500,
        'XML_ID'    => 'EXPIRED',
        'LANG'      => [
            'ru' => ['NAME' => 'Просрочено'],
        ],
    ]);
}