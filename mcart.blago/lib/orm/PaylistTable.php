<?php

namespace Mcart\Blago\ORM;

use Bitrix\Main\Entity;

class PaylistTable extends Entity\DataManager
{

    public static function getTableName()
    {
        return 'm_onec_queue';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true,
                'title' => 'Идентификатор для сортировки',
            )),
            new Entity\StringField('METHOD', array(
                'required' => true,
                'title' => 'Метод отправки',
            )),
            new Entity\StringField('STR_KEY', array(
                'title' => 'Идентификатор окна',
            )),
            new Entity\TextField('DATA', array(
                'required' => true,
                'title' => 'Данные',
            )),
        );
    }
}
