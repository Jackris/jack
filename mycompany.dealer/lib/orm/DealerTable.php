<?php

namespace Mycompany\Dealer\ORM;

use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
class DealerTable extends \Bitrix\Main\ORM\Data\DataManager
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
            (new ManyToMany('CARS', CarModelTable::class))
                ->configureTableName('dealer_to_car')
                ->configureLocalPrimary('ID', 'DEALER_ID')
                ->configureLocalReference('DEALER')
                ->configureRemotePrimary('ID', 'MODEL_ID')
                ->configureRemoteReference('CAR')
        );
    }
}
