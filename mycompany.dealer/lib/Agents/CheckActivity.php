<?php

namespace Mycompany\Dealer\Agents;

use \Bitrix\Main\Loader;

Loader::includeModule("mycompany.dealer");
Loader::includeModule("im");

class CheckActivity
{
    /**
     * Запуск агента, который смотрит на поле "активности" и отсылает уведомление админу (с id 1)
     * @return string
     */
    public static function run()
    {
        $rsDealers = \Mycompany\Dealer\ORM\DealerTable::getList([
            'select' => ['ID', 'NAME', 'ACTIVITY_TIME_START', 'ACTIVITY_TIME_END'],
        ]);
        $curDate = date('d.m.Y');
        $text = '';
        while ($dealer = $rsDealers->fetch()) {
            $stampCurDate = strtotime($curDate);
            $stampStart = strtotime($dealer['ACTIVITY_TIME_START']->format("d.m.Y"));
            $stampEnd = strtotime($dealer['ACTIVITY_TIME_END']->format("d.m.Y"));
            if ($stampCurDate > $stampStart && $stampCurDate == $stampEnd){
                $text .= $dealer['NAME'] . ', ';
            }
        }

        if ($text) {
            print_r('Данный дилер перестал быть активным на ' . date('d.m.Y') . ': ' . $text);
            $arMessageFields = array(
                "TO_USER_ID" => 1,
                //"NOTIFY_TYPE" => IM_NOTIFY_SYSTEM,
                "NOTIFY_MODULE" => "mycompany.dealer",
                "NOTIFY_TAG" => "DEALER",
                "NOTIFY_MESSAGE" => 'Данный дилер перестал быть активным на ' . date('d.m.Y') . ': ' . $text,
            );
            //\CIMNotify::Add($arMessageFields);
        }
        return __CLASS__ . '::run();';
    }
}