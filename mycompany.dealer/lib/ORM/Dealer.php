<?php

namespace Mycompany\Dealer\ORM;

use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;

IncludeModuleLangFile(__FILE__);

class DealerTable extends \Bitrix\Main\ORM\Data\DataManager
{

    public static function getTableName()
    {
        return 'mcart_dealer';
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
                'title' => GetMessage("MCART_DEALER_NAME")
            )),
            new Entity\TextField('ADDRESS', array(
                'title' => GetMessage("MCART_DEALER_ADDRESS")
            )),
            new Entity\IntegerField('COUNT_MANAGERS', array(
                'title' => GetMessage("MCART_DEALER_MANAGERS")
            )),
            new Entity\DateTimeField('ACTIVITY_TIME_START', array(
                'title' => GetMessage("MCART_DEALER_ACTIVITY_START")
            )),
            new Entity\DateTimeField('ACTIVITY_TIME_END', array(
                'title' => GetMessage("MCART_DEALER_ACTIVITY_END")
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
