<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/prolog.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpApplication;

IncludeModuleLangFile(__FILE__);

global $APPLICATION;

$PAGE_ID = "1c";
$MODULE_ID = "mcart.blago";

$MODULE_SUFFIX = "MCART_BLAGO";
$CONFIG_SUFFIX = $MODULE_SUFFIX . "_1C_";

if (!Loader::includeModule($MODULE_ID)) {
    ?>
    <span class="required">
            <?= Loc::getMessage($CONFIG_SUFFIX . 'INCLUDE_MODULE_ERROR', ['#NAME#' => $MODULE_ID]) ?>
        </span>
    <?php
    die;
}

if ($APPLICATION->GetGroupRight($MODULE_ID) !== 'W') {
    $APPLICATION->AuthForm(Loc::getMessage($CONFIG_SUFFIX . 'ACCESS_DENIED'));
}

$APPLICATION->SetTitle(GetMessage($CONFIG_SUFFIX . 'TITLE'));

$aTabs = [];

$arFields['pay_sheet'] = [
    [
        $CONFIG_SUFFIX . 'NAMESPACE',
        Loc::getMessage($CONFIG_SUFFIX . "NAMESPACE"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "NAMESPACE", ""),
        ['text'],
    ],
    [
        $CONFIG_SUFFIX . 'URL',
        Loc::getMessage($CONFIG_SUFFIX . "URL"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "URL", ""),
        ['text'],
    ],
    [
        $CONFIG_SUFFIX . 'PORT',
        Loc::getMessage($CONFIG_SUFFIX . "PORT"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "PORT", ""),
        ['text'],
    ],
    [
        $CONFIG_SUFFIX . 'LOGIN',
        Loc::getMessage($CONFIG_SUFFIX . "LOGIN"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "LOGIN", ""),
        ['text'],
    ],
    [
        $CONFIG_SUFFIX . 'PASSWORD',
        Loc::getMessage($CONFIG_SUFFIX . "PASSWORD"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "PASSWORD", ""),
        ['text'],
    ],
    [
        $CONFIG_SUFFIX . 'TIMEOUT',
        Loc::getMessage($CONFIG_SUFFIX . "TIMEOUT"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "TIMEOUT", ""),
        ['text'],
    ],
];

$aTabs[] = [
    'DIV' => 'pay_sheet_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_PAY_SHEET"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_PAY_SHEET"),
    'OPTIONS' => $arFields['pay_sheet']
];

$request = HttpApplication::getInstance()->getContext()->getRequest();
if ($request->isPost() && check_bitrix_sessid()) {
    try {
        foreach ($aTabs as $aTab) {
            foreach ($aTab['OPTIONS'] as $arOption) {
                if (!is_array($arOption) || empty($arOption)) {
                    continue;
                }
                if ($arOption['note']) {
                    continue;
                }
                if ($request['Update']) {
                    $optionValue = $request->getPost($arOption[0]);
                    Option::set($MODULE_ID, $arOption[0], $optionValue);
                }
            }
        }
        LocalRedirect($APPLICATION->getCurPage() . '?mpage=' . $PAGE_ID);
    } catch (\Bitrix\Main\SystemException|\Bitrix\Main\Error $e) {
        $APPLICATION->ThrowException($e->getMessage());
        if ($e = $APPLICATION->GetException()) {
            $message = new CAdminMessage($CONFIG_SUFFIX . "ERROR", $e);
        }
    }
}

$tabControl = new CAdminTabControl(
    'tabControl',
    $aTabs
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

if ($message) {
    echo $message->Show();
}

$tabControl->begin();
?>
    <form
            action="<?= $APPLICATION->getCurPage() ?>?mpage=<?= $PAGE_ID ?>"
            method="post"
            name="<?= $MODULE_SUFFIX ?>_settings"
            enctype="multipart/form-data" id="<?= $MODULE_SUFFIX ?>">
        <?= bitrix_sessid_post() ?>
        <?php
        foreach ($aTabs as $aTab) {
            if ($aTab['OPTIONS']) {
                $tabControl->beginNextTab();
                __AdmSettingsDrawList($MODULE_ID, $aTab['OPTIONS']);
            }
        } ?>
        <?php
        $tabControl->buttons(); ?>

        <input type="submit" name="Update"
               value="<?= Loc::getMessage($CONFIG_SUFFIX . 'SAVE') ?>"
               class="adm-btn-save"/>
    </form>
<?php
$tabControl->end();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");