<?php

defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

IncludeModuleLangFile(__FILE__);

class mcart_blago extends CModule
{
    var $MODULE_ID = "mcart.blago";
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

        $this->MODULE_NAME = GetMessage("MCART_BLAGO_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("MCART_BLAGO_MODULE_DESCRIPTION");

        $this->PARTNER_NAME = GetMessage("MCART_BLAGO_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("MCART_BLAGO_URL");
    }

    function DoInstall()
    {
        global $APPLICATION;

        if (!IsModuleInstalled($this->MODULE_ID)) {
            ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallEvents();
            $this->InstallFiles();
            $this->InstallDb();

            $eventManager = \Bitrix\Main\EventManager::getInstance();
            $eventManager->registerEventHandlerCompatible("pull", "OnGetDependentModule", $this->MODULE_ID, "\\Mcart\\Blago\\Events", "OnGetPushAndPullDependentModule");

            $GLOBALS["errors"] = $errors;
            $APPLICATION->IncludeAdminFile(
                getMessage("MCART_BLAGO_INSTALL_TITLE"),
                $_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/install/step2.php"
            );
        }
    }

    function DoUninstall()
    {
        global $APPLICATION;

        if (IsModuleInstalled($this->MODULE_ID)) {
            $this->UnInstallEvents();
            $this->UnInstallFiles();

            COption::RemoveOption($this->MODULE_ID);
            ModuleManager::unRegisterModule($this->MODULE_ID);

            $GLOBALS["errors"] = $errors;
            $APPLICATION->IncludeAdminFile(
                getMessage("MCART_BLAGO_UNINSTALL_TITLE"),
                $_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/install/unstep2.php"
            );
        }
    }

    function InstallEvents()
    {
        $manager = \Bitrix\Main\EventManager::getInstance();
        $manager->registerEventHandler(
            "rest",
            "OnRestServiceBuildDescription",
            "mcart.blago",
            "\\Mcart\\Blago\\Api",
            "OnRestServiceBuildDescription"
        );

        return true;
    }

    function UnInstallEvents()
    {
        $manager = \Bitrix\Main\EventManager::getInstance();
        $manager->unRegisterEventHandler(
            "rest",
            "OnRestServiceBuildDescription",
            "mcart.blago",
            "\\Mcart\\Blago\\Api",
            "OnRestServiceBuildDescription"
        );
    }

    function UnInstallFiles($arParams = array())
    {
        DeleteDirFiles(
            $_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/install/admin/",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/"
        );
        DeleteDirFiles(
            $_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/install/panel/main/",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/panel/main/"
        );
        DeleteDirFilesEx("/bitrix/images/mcart.blago");
    }

    function InstallFiles($arParams = array())
    {
        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/install/admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin",
            true,
            true
        );
        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/install/panel",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/panel",
            true,
            true
        );
        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/install/images",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/images",
            true,
            true
        );
    }
    function InstallDb()
    {
        Loader::includeModule($this->MODULE_ID);

        if (
            !\Bitrix\Main\Application::getConnection(\Mcart\Blago\ORM\PaylistTable::getConnectionName())
                ->isTableExists(\Bitrix\Main\Entity\Base::getInstance(\Mcart\Blago\ORM\PaylistTable::class)
                    ->getDBTableName())
        ) {
            \Bitrix\Main\Entity\Base::getInstance(\Mcart\Blago\ORM\PaylistTable::class)->createDbTable();
        }

        if (
            !\Bitrix\Main\Application::getConnection(\Mcart\Blago\ORM\GroupSettingsMenuTable::getConnectionName())
                ->isTableExists(\Bitrix\Main\Entity\Base::getInstance(\Mcart\Blago\ORM\GroupSettingsMenuTable::class)
                    ->getDBTableName())
        ) {
            \Bitrix\Main\Entity\Base::getInstance(\Mcart\Blago\ORM\GroupSettingsMenuTable::class)->createDbTable();
        }

        if (
            !\Bitrix\Main\Application::getConnection(\Mcart\Blago\ORM\NotifyTable::getConnectionName())
                ->isTableExists(\Bitrix\Main\Entity\Base::getInstance(\Mcart\Blago\ORM\NotifyTable::class)
                    ->getDBTableName())
        ) {
            \Bitrix\Main\Entity\Base::getInstance(\Mcart\Blago\ORM\NotifyTable::class)->createDbTable();
        }
    }

    function GetModuleRightList()
    {
        $arr = [
            "reference_id" => ["D", "R", "W"],
            "reference" => [
                "[D] " . GetMessage("MCART_BLAGO_RIGHT_D"),
                "[R] " . GetMessage("MCART_BLAGO_RIGHT_R"),
                "[W] " . GetMessage("MCART_BLAGO_RIGHT_W"),
            ],
        ];
        return $arr;
    }
}