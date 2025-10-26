<?php

use Bitrix\Main\Context;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\Extension;
use Bitrix\Crm\Service\Container;
use Bitrix\Main\Loader;

class Handler
{
    public static function dealUpdate(&$arFields)
    {
        $rsDeal = CCrmDeal::GetListEx(
            [],
            ['ID' => $arFields['ID']],
            false,
            false,
            ['STAGE_ID']
        );
        $prevDealStage = '';
        if ($prevDeal = $rsDeal->Fetch()){
            $prevDealStage = $prevDeal['STAGE_ID'];
        }

        if ($arFields['STAGE_ID'] && $prevDealStage !== $arFields['STAGE_ID']){
            $stageList = Bitrix\Crm\Category\DealCategory::getStageList('Общая');
            $text = 'ID сделки: ' . $arFields['ID'] . '<br> Стадия: ' . $stageList[$prevDealStage]
                . ' => ' . $stageList[$arFields['STAGE_ID']] . '<br>' . 'Время изменения: ' . $arFields['MOVED_TIME']
                . '<br>' . 'Пользователь, инициировавший изменение: ' . $arFields['MODIFY_BY_ID'];
            \CEventLog::Add([
                'SEVERITY' => 'INFO',
                'AUDIT_TYPE_ID' => 'DEAL_UPDATE',
                'MODULE_ID' => 'crm',
                'DESCRIPTION' => $text,
            ]);
        }
    }
}
