<?php

namespace Mycompany\Dealer\ORM;

use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\ORM\Fields\Relations\Reference;

class DealerToCarTable extends Entity\DataManager
{

    public static function getTableName()
    {
        return 'dealer_to_car';
    }

    public static function getMap()
    {
/*        return array(
            new Entity\IntegerField('DEALER_ID', array(
                'primary' => true,
                'title' => 'Идентификатор дилера',
            )),
            new Entity\ReferenceField(
                'DEALER',
                'Mycompany\Dealer\ORM\DealerTable',
                array('=this.DEALER_ID' => 'ref.ID')
            ),
            new Entity\IntegerField('MODEL_ID', array(
                'title' => 'Идентификатор модели',
            )),
            new Entity\ReferenceField(
                'MODEL',
                'Mycompany\Dealer\ORM\CarModelTable',
                array('=this.MODEL_ID' => 'ref.ID')
            )
        );*/
        return([
            (new Entity\IntegerField('DEALER_ID'))
                ->configurePrimary(true),
            (new Reference('DEALER', DealerTable::class,
                Join::on('this.DEALER_ID', 'ref.ID')))
                ->configureJoinType('inner'),
            (new Entity\IntegerField('MODEL_ID'))
                ->configurePrimary(true),
            (new Reference('MODEL', CarModelTable::class,
                Join::on('this.MODEL_ID', 'ref.ID')))
                ->configureJoinType('inner'),
        ]);

    }
    public static function onBeforeAdd(Entity\Event $event)
    {
/*        $entity = $event->getEntity();
        $entityDataClass = $entity->GetDataClass();
        $eventType = $event->getEventType();
        $arFields = $event->getParameter("fields");
        $arParameters = $event->getParameters();
Option::get($MODULE_ID, "rules_link_idea", ''),
        $result = new \Bitrix\Main\Entity\EventResult();

        if ($count >= $settingsCount) {
            $arErrors = Array();
            $arErrors[] = new \Bitrix\Main\Entity\FieldError($entity->getField("UF_DESCRIPTION"), "Ошибка в поле UF_DESCRIPTION. Поле не должно быть пустым!");
            $result->setErrors($arErrors);
        }

        return $result;*/
    }
}
