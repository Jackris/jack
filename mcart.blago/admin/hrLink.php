<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/prolog.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpApplication;

IncludeModuleLangFile(__FILE__);

global $APPLICATION;

$PAGE_ID = "hrLink";
$MODULE_ID = "mcart.blago";

$MODULE_SUFFIX = "MCART_BLAGO";
$CONFIG_SUFFIX = $MODULE_SUFFIX . "_HR_LINK_";

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

$arFields['data_token'] = [
    [
        $CONFIG_SUFFIX . 'ISSUER',
        Loc::getMessage($CONFIG_SUFFIX . "ISSUER"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "ISSUER", ""),
        ['text'],
    ],
    [
        $CONFIG_SUFFIX . 'SUBJECT',
        Loc::getMessage($CONFIG_SUFFIX . "SUBJECT"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "SUBJECT", ""),
        ['text'],
    ],
    [
        $CONFIG_SUFFIX . 'SUPER_USER_ID',
        Loc::getMessage($CONFIG_SUFFIX . "SUPER_USER_ID"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "SUPER_USER_ID", ""),
        ['text'],
    ],
    [
        $CONFIG_SUFFIX . 'PUB_KEY',
        Loc::getMessage($CONFIG_SUFFIX . "PUB_KEY"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "PUB_KEY", ""),
        ['textarea', '5'],
    ],
    [
        $CONFIG_SUFFIX . 'SEC_KEY',
        Loc::getMessage($CONFIG_SUFFIX . "SEC_KEY"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "SEC_KEY", ""),
        ['textarea', '5'],
    ],
    [
        $CONFIG_SUFFIX . 'SERVER_NAME',
        Loc::getMessage($CONFIG_SUFFIX . "SERVER_NAME"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "SERVER_NAME", ""),
        ['text'],
    ],
    [
        $CONFIG_SUFFIX . 'URL',
        Loc::getMessage($CONFIG_SUFFIX . "URL"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "URL", ""),
        ['text'],
    ],
    [
        $CONFIG_SUFFIX . 'CLIENT_ID',
        Loc::getMessage($CONFIG_SUFFIX . "CLIENT_ID"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "CLIENT_ID", ""),
        ['text'],
    ],
    [
        $CONFIG_SUFFIX . 'TYPE_DOC',
        Loc::getMessage($CONFIG_SUFFIX . "TYPE_DOC"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "TYPE_DOC", ""),
        ['text'],
    ],
];

$aTabs[] = [
    'DIV' => 'data_token_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_DATA_TOKEN"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_DATA_TOKEN"),
    'OPTIONS' => $arFields['data_token']
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