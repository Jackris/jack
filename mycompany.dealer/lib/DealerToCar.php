<?php

namespace Mycompany\Dealer;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\ORM\Fields\Relations\Reference;

class DealerToCarTable extends Entity\DataManager
{
    private const MODULE_ID = "mycompany.dealer";

    public static function getTableName()
    {
        return 'mcart_dealer_to_car';
    }

    public static function getMap()
    {
        return ([
            (new Entity\IntegerField('DEALER_ID')),
            (new Reference(
                'DEALER', DealerTable::class,
                Join::on('this.DEALER_ID', 'ref.ID')
            ))
                ->configureJoinType('inner'),
            (new Entity\IntegerField('MODEL_ID'))
                ->configurePrimary(true),
            (new Reference(
                'CAR', CarModelTable::class,
                Join::on('this.MODEL_ID', 'ref.ID')
            ))
                ->configureJoinType('inner'),
        ]);
    }

    public static function onBeforeAdd(Entity\Event $event)
    {
        $entity = $event->getEntity();
        $entityDataClass = $entity->GetDataClass();
        $arFields = $event->getParameter("fields");

        //Получение макс. кол-ва моделей на дилера
        $settingsCount = Option::get(self::MODULE_ID, "max_count_models", '');
        //Получение текущее кол-ва моделей на дилера
        $count = $entityDataClass::getCount(['DEALER_ID' => $arFields['DEALER_ID']]);
        $result = new \Bitrix\Main\Entity\EventResult();
        if ($count >= $settingsCount) {
            $result->addError(new Entity\EntityError('Максимальное количество моделей достигнуто для данного дилера'));
            return $result;
        }
        return $result;
    }
}