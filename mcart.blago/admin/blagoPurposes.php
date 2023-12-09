<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/prolog.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpApplication;

CUtil::InitJSCore(array('jquery'));
IncludeModuleLangFile(__FILE__);

global $APPLICATION;

$PAGE_ID = "blagoPurposes";
$MODULE_ID = "mcart.blago";

$MODULE_SUFFIX = "MCART_BLAGO";
$CONFIG_SUFFIX = $MODULE_SUFFIX . "_PURPOSES_";

if (!Loader::includeModule($MODULE_ID)) {
    ?>
    <span class="required">
            <?= Loc::getMessage($CONFIG_SUFFIX . 'INCLUDE_MODULE_ERROR', ['#NAME#' => $MODULE_ID]) ?>
        </span>
    <?php
    die;
}

if ($APPLICATION->GetGroupRight($MODULE_ID) === 'D') {
    $APPLICATION->AuthForm(Loc::getMessage($CONFIG_SUFFIX . 'ACCESS_DENIED'));
}

$APPLICATION->SetTitle(GetMessage($CONFIG_SUFFIX . 'TITLE'));

$aTabs = [];

$arFields['generate_file'] = [

];

$aTabs[] = [
    'DIV' => 'generate_file_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_GENERATE_FILE"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_GENERATE_FILE"),
    'OPTIONS' => $arFields['generate_file']
];

$selectedFiles = [];
$preSelectedFiles = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "GENERATE_FILE", ""), true);
if (is_array($preSelectedFiles)) {
    $selectedFiles = $preSelectedFiles;
}

$request = HttpApplication::getInstance()->getContext()->getRequest();
if ($request->isPost() && check_bitrix_sessid()) {
    try {
        $selectedFilesPost = $request->getPost($CONFIG_SUFFIX . "GENERATE_FILE");
        if (is_array($selectedFilesPost)) {
            if (empty($selectedFilesPost['fileId'])) {
                $arFile = \Bitrix\Main\UI\FileInput::prepareFile($selectedFilesPost);
                if (isset($arFile['tmp_name']) && !file_exists($arFile['tmp_name'])) {
                    $tmpFilesDir = \CTempFile::GetAbsoluteRoot();
                    $arFile['tmp_name'] = $tmpFilesDir . $arFile['tmp_name'];
                }
                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files')) {
                    mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files');
                }
                $saveFileId = CFile::SaveFile($arFile, 'option_files');
                $selectedFilesPost['fileId'] = $saveFileId;
            }
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "GENERATE_FILE", json_encode($selectedFilesPost));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "GENERATE_FILE", json_encode([]));
        }
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
            $tabControl->beginNextTab();

            if ($aTab['OPTIONS']) {
                __AdmSettingsDrawList($MODULE_ID, $aTab['OPTIONS']);
            }
            if ($aTab['DIV'] === 'generate_file_settings') {
                ?>

                <tr>
                    <td width="40%" class="adm-detail-valign-top"><?= Loc::getMessage(
                            $CONFIG_SUFFIX . 'FIELD_GENERATE_FILE'
                        ) ?></td>
                    <td width="60%">
                        <?php
                        echo \Bitrix\Main\UI\FileInput::createInstance(array(
                            "name" => $CONFIG_SUFFIX . 'GENERATE_FILE',
                            "description" => true,
                            "upload" => true,
                            "allowUpload" => "F",
                            "medialib" => true,
                            "fileDialog" => true,
                            "cloud" => true,
                            "delete" => true,
                            "maxCount" => 1
                        ))->show(
                            $selectedFiles['fileId'] ? $selectedFiles['fileId'] : ($selectedFiles['prevFileId'] ? $selectedFiles['prevFileId'] : 0)
                        );
                        ?>
                        <?php
                        if (!empty($selectedFiles['fileId']) || !empty($selectedFiles['prevFileId'])): ?>
                            <input type="hidden" class="oldVideo"
                                   name="<?= $CONFIG_SUFFIX ?>GENERATE_FILE[prevFileId]"
                                   value="<?= $selectedFiles['fileId'] ? $selectedFiles['fileId'] : $selectedFiles['prevFileId'] ?>">
                        <?php
                        endif; ?>
                    </td>
                </tr>
                <?php
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
