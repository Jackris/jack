<?php

namespace Mcart\Blago\ORM;

use Bitrix\Main\Entity;

class NotifyTable extends Entity\DataManager
{

    public static function getTableName()
    {
        return 'm_notify';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true,
                'title' => 'Идентификатор для сортировки',
            )),
            new Entity\StringField('SECTION_CODE', array(
                'required' => true,
                'title' => 'Код секции уведомления',
            )),
            new Entity\StringField('NOTIFY_CODE', array(
                'title' => 'Код уведомления',
            )),
            new Entity\TextField('USER_IDS', array(
                'required' => true,
                'title' => 'Пользователи',
            )),
            new Entity\TextField('DATA', array(
                'required' => true,
                'title' => 'Данные',
            )),
        );
    }
}
