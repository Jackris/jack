<?php

namespace Mycompany\Dealer\ORM;

use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;

class CarModelTable extends \Bitrix\Main\ORM\Data\DataManager
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
            (new ManyToMany('BOOKS', DealerTable::class))
                ->configureTableName('b_book_author')
                ->configureLocalPrimary('BOOK_ID', 'ID')
                ->configureLocalReference('MY_BOOK')
                ->configureRemotePrimary('BOOK_ID', 'ID')
                ->configureRemoteReference('MY_AUTHOR')
        );
    }
}
