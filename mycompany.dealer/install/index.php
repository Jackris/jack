<?php

defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

IncludeModuleLangFile(__FILE__);

/**
 * Модуль, создающий связанные между собой ДИЛЕРОВ и МОДЕЛЕЙ авто, которые они продают
 */
class mycompany_dealer extends CModule
{
    public $MODULE_ID = "mycompany.dealer";
    public $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;

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

    /**
     * Установка модуля
     * @return void
     */
    function DoInstall()
    {
        global $APPLICATION;

        if (!IsModuleInstalled($this->MODULE_ID)) {
            ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallEvents();
            $this->InstallFiles();
            $this->InstallDb();
            $this->addAgent();
        }
    }

    /**
     * Удаление модуля
     * @return void
     */
    function DoUninstall()
    {
        global $APPLICATION;

        if (IsModuleInstalled($this->MODULE_ID)) {
            $this->UnInstallEvents();
            $this->UnInstallFiles();
            $this->UnInstallDB();
            $this->deleteAgent();
            COption::RemoveOption($this->MODULE_ID);
        }
    }

    /**
     * Создание таблиц
     * @return void
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    function InstallDb()
    {
        Loader::includeModule($this->MODULE_ID);

        if (
            !\Bitrix\Main\Application::getConnection(\Mycompany\Dealer\ORM\CarModelTable::getConnectionName())
                ->isTableExists(
                    \Bitrix\Main\Entity\Base::getInstance(\Mycompany\Dealer\ORM\CarModelTable::class)
                        ->getDBTableName()
                )
        ) {
            \Bitrix\Main\Entity\Base::getInstance(\Mycompany\Dealer\ORM\CarModelTable::class)->createDbTable();
        }

        if (
            !\Bitrix\Main\Application::getConnection(\Mycompany\Dealer\ORM\DealerTable::getConnectionName())
                ->isTableExists(
                    \Bitrix\Main\Entity\Base::getInstance(\Mycompany\Dealer\ORM\DealerTable::class)
                        ->getDBTableName()
                )
        ) {
            \Bitrix\Main\Entity\Base::getInstance(\Mycompany\Dealer\ORM\DealerTable::class)->createDbTable();
        }

        if (
            !\Bitrix\Main\Application::getConnection(\Mycompany\Dealer\ORM\DealerToCarTable::getConnectionName())
                ->isTableExists(
                    \Bitrix\Main\Entity\Base::getInstance(\Mycompany\Dealer\ORM\DealerToCarTable::class)
                        ->getDBTableName()
                )
        ) {
            \Bitrix\Main\Entity\Base::getInstance(\Mycompany\Dealer\ORM\DealerToCarTable::class)->createDbTable();
        }
    }

    /**
     * Удаление таблиц
     * @return void
     */
    function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);
        \Bitrix\Main\Application::getConnection(\Mycompany\Dealer\ORM\CarModelTable::getConnectionName())
            ->queryExecute(
                'drop table if exists ' . \Bitrix\Main\Entity\Base::getInstance(
                    '\Mycompany\Dealer\ORM\CarModelTable'
                )->getDBTableName()
            );

        \Bitrix\Main\Application::getConnection(\Mycompany\Dealer\ORM\DealerTable::getConnectionName())
            ->queryExecute(
                'drop table if exists ' . \Bitrix\Main\Entity\Base::getInstance(
                    '\Mycompany\Dealer\ORM\DealerTable'
                )->getDBTableName()
            );

        \Bitrix\Main\Application::getConnection(\Mycompany\Dealer\ORM\DealerToCarTable::getConnectionName())
            ->queryExecute(
                'drop table if exists ' . \Bitrix\Main\Entity\Base::getInstance(
                    '\Mycompany\Dealer\ORM\DealerToCarTable'
                )->getDBTableName()
            );

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * Добавление агентов
     * @return void
     */
    public function addAgent()
    {
        $stmp = AddToTimeStamp(array("HH" => 24), time());
        $time = ConvertTimeStamp($stmp) . ' 08:00:00';
        \CAgent::AddAgent(
            "Mycompany\Dealer\Agents\CheckActivity::run();",
            "mycompany.dealer",
            "N",
            86400,
            "",
            "Y",
            $time,
            10
        );
    }

    /**
     * Удаление агентов
     * @return void
     */
    public function deleteAgent()
    {
        CAgent::RemoveModuleAgents($this->MODULE_ID);
    }

}