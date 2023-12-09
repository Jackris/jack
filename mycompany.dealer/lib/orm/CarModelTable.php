<?php

namespace Mycompany\Dealer\ORM;

use Bitrix\Main\Entity;

class CarModelTable extends Entity\DataManager
{

    public static function getTableName()
    {
        return 'car_model';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true,
                'title' => 'Идентификатор',
            )),
            new Entity\StringField('MODEL', array(
                'title' => 'Модель машины',
            )),
            new Entity\IntegerField('YEAR_MADY', array(
                'title' => 'Год производства',
            )),
            new Entity\IntegerField('CAPACITY', array(
                'title' => 'Объем двигателя',
            )),
        );
    }
}
