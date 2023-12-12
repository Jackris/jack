<?php

namespace Mycompany\Dealer;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;

IncludeModuleLangFile(__FILE__);

class CarModelTable extends \Bitrix\Main\ORM\Data\DataManager
{

    public static function getTableName()
    {
        return 'mcart_car_model';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true,
                'title' => GetMessage("MCART_CAR_ID")
            )),
            new Entity\StringField('MODEL', array(
                'title' => GetMessage("MCART_CAR_MODEL")
            )),
            new Entity\IntegerField('YEAR_MADY', array(
                'title' => GetMessage("MCART_CAR_YEAR")
            )),
            new Entity\IntegerField('CAPACITY', array(
                'title' => GetMessage("MCART_CAR_CAPACITY")
            )),
            (new ManyToMany('DEALERS', DealerTable::class))
                ->configureTableName('dealer_to_car')
                ->configureLocalPrimary('ID', 'MODEL_ID')
                ->configureLocalReference('CAR')
                ->configureRemotePrimary('ID', 'DEALER_ID')
                ->configureRemoteReference('DEALER')
        );
    }

    /**
     * Удаление связанных позиций у дилера
     * @param Entity\Event $event
     * @return Entity\EventResult
     */
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
