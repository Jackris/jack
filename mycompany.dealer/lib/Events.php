<?php

namespace Mycompany\Dealer;

use Bitrix\Main\SystemException;

class Events
{
    private const MODULE_ID = 'mycompany.dealer';

    public static function OnGetPushAndPullDependentModule()
    {
        return array(
            'MODULE_ID' => self::MODULE_ID,
            'USE' => ["PUBLIC_SECTION"]
        );
    }
}