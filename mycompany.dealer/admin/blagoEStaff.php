<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/prolog.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpApplication;

CUtil::InitJSCore(array('jquery'));
\Bitrix\Main\Page\Asset::getInstance()->addString(
    '
<style>
.ip_input{
margin-bottom: 3px !important;
}
</style>
'
);

IncludeModuleLangFile(__FILE__);

global $APPLICATION;

$PAGE_ID = "eStaff";
$MODULE_ID = "mcart.blago";

$MODULE_SUFFIX = "MCART_BLAGO";
$CONFIG_SUFFIX = $MODULE_SUFFIX . "_ESTAFF_";

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
        $CONFIG_SUFFIX . 'SERVER',
        Loc::getMessage($CONFIG_SUFFIX . "SERVER"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "SERVER", ""),
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
    ]
];

$aTabs[] = [
    'DIV' => 'data_token_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "NAME_TITLE"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "NAME_TITLE"),
    'OPTIONS' => $arFields['data_token']
];
$selectedKiosks = [];
$preSelectedKiosks = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "LIST", ""), true);
if (is_array($preSelectedKiosks)) {
    $selectedKiosks = $preSelectedKiosks;
}
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
        $directionDeps = $request->getPost($CONFIG_SUFFIX . "LIST");
        if (is_array($directionDeps)) {
            foreach ($directionDeps as $key=> $dep){
                $dep=trim($dep);
                if(empty($dep)){
                    unset($directionDeps[$key]);
                }else{
                    $directionDeps[$key]= $dep;
                }

            }
            if(empty($directionDeps)){
                Option::set($MODULE_ID, $CONFIG_SUFFIX . "LIST", json_encode([]));
            }else{
                Option::set($MODULE_ID, $CONFIG_SUFFIX . "LIST",json_encode($directionDeps));
            }
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "LIST", json_encode([]));
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
            if ($aTab['DIV']==='data_token_settings'){
                ?>
                <tr class="kiosks_wrapper">
                    <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%" >
                        <?=Loc::getMessage($CONFIG_SUFFIX . "LIST") ?>
                    </td>
                    <?if(!empty($selectedKiosks)):?>
                        <td  width="60%" class="adm-detail-content-cell-r">
                            <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%" id="tbEXCL_IPS">
                                <tbody>
                                <td class="kiosk_ip__container">
                                    <?foreach ($selectedKiosks as $key=>$kiosk):?>
                                        <input type="text" class="ip_input" value="<?=$kiosk?>" name="MCART_BLAGO_ESTAFF_LIST[]"><br>
                                    <?endforeach;?>
                                </td>
                                <tr>
                                    <td>
                                        <br><input type="button" id="addButton" value="Добавить">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    <?else:?>
                        <td  width="60%" class="adm-detail-content-cell-r">
                            <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%" id="tbEXCL_IPS">
                                <tbody>
                                <td class="kiosk_ip__container">
                                    <input type="text" class="ip_input" value="" name="MCART_BLAGO_ESTAFF_LIST[]"><br>
                                </td>
                                <tr>
                                    <td>
                                        <br><input type="button" id="addButton" value="Добавить">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    <?endif;?>

                </tr>
                <?php
            }
        } ?>
        <?php
        $tabControl->buttons(); ?>
        <script>
            $("body").on("click", "#addButton", function(evt) {
                let container=  evt.currentTarget.closest('table').querySelector('td.kiosk_ip__container');
                let input= BX.create('input', {
                    props: {
                        className:'ip_input',
                        name:'MCART_BLAGO_ESTAFF_LIST[]',
                        type:'text'
                    },
                });
                let br = BX.create('br', {});
                container.append(input);
                container.append(br);
            });
        </script>
        <input type="submit" name="Update"
               value="<?= Loc::getMessage($CONFIG_SUFFIX . 'SAVE') ?>"
               class="adm-btn-save"/>
    </form>

<?php
$tabControl->end();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");