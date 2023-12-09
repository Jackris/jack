<?php

namespace Mcart\Blago\ORM;

use Bitrix\Main\Entity;

class GroupSettingsMenuTable extends Entity\DataManager
{

    public static function getTableName()
    {
        return 'm_group_settings_menu';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'required' => true,
                'title' => 'Группа',
            )),
            new Entity\TextField('ITEMS', array(
                'required' => true,
                'title' => 'Пункты меню',
            )),
        );
    }
}
