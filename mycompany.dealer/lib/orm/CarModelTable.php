<?php

namespace Mycompany\Dealer\ORM;

use Bitrix\Main\Config\Option;
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
            (new ManyToMany('DEALERS', DealerTable::class))
                ->configureTableName('dealer_to_car')
                ->configureLocalPrimary('ID', 'MODEL_ID')
                ->configureLocalReference('CAR')
                ->configureRemotePrimary('ID', 'DEALER_ID')
                ->configureRemoteReference('DEALER')
        );
    }
    public static function onAfterDelete(Entity\Event $event)
    {
        $entity = $event->getEntity();
        $arParameters = $event->getParameters();
        $result = new \Bitrix\Main\Entity\EventResult();
        \Mycompany\Dealer\ORM\DealerToCarTable::delete(
            ['MODEL_ID' => $arParameters['id']['ID']]
        );
        return $result;
    }
}
