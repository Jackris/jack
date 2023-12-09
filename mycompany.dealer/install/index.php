<?php

defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

IncludeModuleLangFile(__FILE__);

class mycompany_dealer extends CModule
{
    var $MODULE_ID = "mycompany.dealer";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;

    var $MODULE_GROUP_RIGHTS = "Y";

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . "/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = 'Модуль "ДИЛЕРЫ"';
        $this->MODULE_DESCRIPTION = 'Описание модуля "ДИЛЕРЫ"';

        $this->PARTNER_NAME = 'mycompany';
        $this->PARTNER_URI = 'джакрис.рф';
    }

    function DoInstall()
    {
        global $APPLICATION;

        if (!IsModuleInstalled($this->MODULE_ID)) {
            ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallEvents();
            $this->InstallFiles();
            $this->InstallDb();
        }
    }

    function DoUninstall()
    {
        global $APPLICATION;

        if (IsModuleInstalled($this->MODULE_ID)) {
            $this->UnInstallEvents();
            $this->UnInstallFiles();
            $this->UnInstallDB();
            COption::RemoveOption($this->MODULE_ID);
        }
    }

    function InstallEvents()
    {
        //$manager = \Bitrix\Main\EventManager::getInstance();
/*        \Bitrix\Main\EventManager::getInstance()->registerEventHandler(
            self::$MODULE_ID,
            'Mycompany\Dealer\ORM\DealerToCarTable::onBeforeAdd',
            self::$MODULE_ID,
            self::class,
            'onVirtualClassBuildList'
        );*/
        return true;
    }

    function UnInstallEvents()
    {
        $manager = \Bitrix\Main\EventManager::getInstance();
    }

    function UnInstallFiles($arParams = array())
    {
    }

    function InstallFiles($arParams = array())
    {
    }
    function InstallDb()
    {
        Loader::includeModule($this->MODULE_ID);

        if (
            !\Bitrix\Main\Application::getConnection(\Mycompany\Dealer\ORM\CarModelTable::getConnectionName())
                ->isTableExists(\Bitrix\Main\Entity\Base::getInstance(\Mycompany\Dealer\ORM\CarModelTable::class)
                    ->getDBTableName())
        ) {
            \Bitrix\Main\Entity\Base::getInstance(\Mycompany\Dealer\ORM\CarModelTable::class)->createDbTable();
        }

        if (
            !\Bitrix\Main\Application::getConnection(\Mycompany\Dealer\ORM\DealerTable::getConnectionName())
                ->isTableExists(\Bitrix\Main\Entity\Base::getInstance(\Mycompany\Dealer\ORM\DealerTable::class)
                    ->getDBTableName())
        ) {
            \Bitrix\Main\Entity\Base::getInstance(\Mycompany\Dealer\ORM\DealerTable::class)->createDbTable();
        }

        if (
            !\Bitrix\Main\Application::getConnection(\Mycompany\Dealer\ORM\DealerToCarTable::getConnectionName())
                ->isTableExists(\Bitrix\Main\Entity\Base::getInstance(\Mycompany\Dealer\ORM\DealerToCarTable::class)
                    ->getDBTableName())
        ) {
            \Bitrix\Main\Entity\Base::getInstance(\Mycompany\Dealer\ORM\DealerToCarTable::class)->createDbTable();
        }
    }

    function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);
        \Bitrix\Main\Application::getConnection(\Mycompany\Dealer\ORM\CarModelTable::getConnectionName())
            ->queryExecute('drop table if exists ' . \Bitrix\Main\Entity\Base::getInstance('\Mycompany\Dealer\ORM\CarModelTable')->getDBTableName());

        \Bitrix\Main\Application::getConnection(\Mycompany\Dealer\ORM\DealerTable::getConnectionName())
            ->queryExecute('drop table if exists ' . \Bitrix\Main\Entity\Base::getInstance('\Mycompany\Dealer\ORM\DealerTable')->getDBTableName());

        \Bitrix\Main\Application::getConnection(\Mycompany\Dealer\ORM\DealerToCarTable::getConnectionName())
            ->queryExecute('drop table if exists ' . \Bitrix\Main\Entity\Base::getInstance('\Mycompany\Dealer\ORM\DealerToCarTable')->getDBTableName());

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}