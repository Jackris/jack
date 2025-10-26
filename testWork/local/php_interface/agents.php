<?php

use Bitrix\Main\Config\Option;

class Agents
{
    private static $OPTION_NAME = 'deal_checker_new_stage';

    public static function checkDeals()
    {
        try {
            if (!\Bitrix\Main\Loader::includeModule('crm')) {
                return '\\' . __CLASS__ . '::checkDeals();';
            }

            $resDeals = \CCrmDeal::GetList([],
                ['CHECK_PERMISSIONS' => 'N'],
                ['ID', 'STAGE_ID', 'DATE_MODIFY']
            );
            $entityDeal = new \CCrmDeal(false);
            $arFields = ['STAGE_ID' => 'EXPIRED', 'COMMENTS' => 'Автоматический перевод из-за просрочки'];
            while ($deal = $resDeals->fetch()) {
                if ($deal['STAGE_ID'] === 'NEW') {
                    $daysDiff = (strtotime(date('d.m.Y H:i:s')) - strtotime($deal['DATE_MODIFY'])) / 86400;
                    $days = Option::get('crm', self::$OPTION_NAME . $deal['ID'], 0);
                    if ($days > 3 || $daysDiff > 3) {
                        $entityDeal->Update($deal['ID'], $arFields, true, true);
                        Option::delete('crm', ['name' => self::$OPTION_NAME . $deal['ID']]);
                    } else {
                        Option::set('crm', self::$OPTION_NAME . $deal['ID'], $days + 1);
                    }
                }
            }
        } catch (\Bitrix\Main\SystemException|Exception $e) {
            \CEventLog::Add([
                'SEVERITY' => 'ERROR',
                'AUDIT_TYPE_ID' => 'CUSTOM_DEAL',
                'MODULE_ID' => 'crm',
                'DESCRIPTION' => $e->getMessage(),
            ]);
        }
        \CEventLog::Add([
            'SEVERITY' => 'INFO',
            'AUDIT_TYPE_ID' => 'CUSTOM_DEAL',
            'MODULE_ID' => 'crm',
            'DESCRIPTION' => 'Агент успешно отработал',
        ]);

        return '\\' . __CLASS__ . '::checkDeals();';
    }
}
