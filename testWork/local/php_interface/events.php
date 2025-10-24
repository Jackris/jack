<?php

$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->addEventHandler('crm', 'OnBeforeCrmDealUpdate', ['Handler', 'dealUpdate']);

unset($eventManager);
