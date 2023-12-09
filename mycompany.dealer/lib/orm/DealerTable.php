<?php

namespace Mycompany\Dealer\ORM;

use Bitrix\Main\Entity;

class DealerTable extends Entity\DataManager
{

    public static function getTableName()
    {
        return 'dealer';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            new Entity\StringField('NAME', array(
                'required' => true,
                'title' => 'Название',
            )),
            new Entity\TextField('ADDRESS', array(
                'title' => 'Адрес',
            )),
            new Entity\IntegerField('COUNT_MANAGERS', array(
                'title' => 'Количество менеджеров в штате',
            )),
            new Entity\TextField('ACTIVITY_TIME', array(
                'title' => 'Дата и время активности',
            )),
        );
    }
}
