<?php

namespace Mcart\Blago;

use Bitrix\Main\SystemException;

class Events
{
    private const MODULE_ID = 'mcart.blago';

    public static function OnGetPushAndPullDependentModule()
    {
        return array(
            'MODULE_ID' => self::MODULE_ID,
            'USE' => ["PUBLIC_SECTION"]
        );
    }
}