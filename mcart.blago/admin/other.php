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
<script src="/local/modules/mcart.blago/admin/select2.min.js"></script>
<link rel="stylesheet" href="/local/modules/mcart.blago/admin/select2.min.css"/>
<style>
.select2-container{
    min-width: 360px;
    width: min-content !important;
}
.select2-selection__choice{
    width: 100%;
}
.select2-selection__rendered{
    display: grid;
}
</style>
'
);

IncludeModuleLangFile(__FILE__);

global $APPLICATION;

$PAGE_ID = "other";
$MODULE_ID = "mcart.blago";

$MODULE_SUFFIX = "MCART_BLAGO";
$CONFIG_SUFFIX = $MODULE_SUFFIX . "_OTHER_";

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

$iblockId = \Mcart\Iblock\Helper::getIDByCode("departments");

$selectedDeps = [];
$preSelectedDeps = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "DIRECTION_DEPS", ""), true);
if (is_array($preSelectedDeps)) {
    $selectedDeps = $preSelectedDeps;
}

$deps = [];
if ((int)$iblockId > 0) {
    $arFilter = array('IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y');
    $dbDeps = \CIBlockSection::GetList(array("NAME" => "ASC"), $arFilter, true);
    while ($dep = $dbDeps->fetch()) {
        $deps[$dep['ID']] = $dep['NAME'] . "(" . $dep['ID'] . ")";
    }
}

$selectedComps = [];
$preSelectedDeps = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "COMPANY_DEPS", ""), true);
if (is_array($preSelectedDeps)) {
    $selectedComps = $preSelectedDeps;
}

$selectedAreas = [];
$preSelectedDeps = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "AREA_DEPS", ""), true);
if (is_array($preSelectedDeps)) {
    $selectedAreas = $preSelectedDeps;
}

$selectedFiles = [];
$preSelectedFiles = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "REMINDER_WELCOME_VIDEO_TWO", ""), true);
if (is_array($preSelectedFiles)) {
    $selectedFiles = $preSelectedFiles;
}
$selectedWelcomeFiles = [];
$preSelectedWelcomeFiles = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "REMINDER_WELCOME_VIDEO", ""), true);
if (is_array($preSelectedWelcomeFiles)) {
    $selectedWelcomeFiles = $preSelectedWelcomeFiles;
}
$welcomeText = Option::get($MODULE_ID, $CONFIG_SUFFIX . "REMINDER_WELCOME_TEXT", "");

$selectedVmiStaffSchedule = Option::get($MODULE_ID, $CONFIG_SUFFIX . "VMI_STAFF_SCHEDULE", "");


$aTabs = [];

$arFields['deps'] = [];

$aTabs[] = [
    'DIV' => 'deps_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_DEPS"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_DEPS"),
    'OPTIONS' => $arFields['deps']
];

$arFields['birthday'] = [
    [
        $CONFIG_SUFFIX . 'BIRTHDAY_FIRST_NOTIFY',
        Loc::getMessage($CONFIG_SUFFIX . "BIRTHDAY_FIRST_NOTIFY"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "BIRTHDAY_FIRST_NOTIFY", ""),
        ['text'],
    ],
    [
        $CONFIG_SUFFIX . 'BIRTHDAY_TWO_NOTIFY',
        Loc::getMessage($CONFIG_SUFFIX . "BIRTHDAY_TWO_NOTIFY"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "BIRTHDAY_TWO_NOTIFY", ""),
        ['text'],
    ],
];

$aTabs[] = [
    'DIV' => 'birthday_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_BIRTHDAY"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_BIRTHDAY"),
    'OPTIONS' => $arFields['birthday']
];

if($APPLICATION->GetGroupRight($MODULE_ID) === 'W'){
    $arFields['sms'] = [
        [
            $CONFIG_SUFFIX . 'SMS_URL',
            Loc::getMessage($CONFIG_SUFFIX . "SMS_URL"),
            Option::get($MODULE_ID, $CONFIG_SUFFIX . "SMS_URL", ""),
            ['text'],
        ],
        [
            $CONFIG_SUFFIX . 'SMS_KEY',
            Loc::getMessage($CONFIG_SUFFIX . "SMS_KEY"),
            Option::get($MODULE_ID, $CONFIG_SUFFIX . "SMS_KEY", ""),
            ['text'],
        ],
        [
            $CONFIG_SUFFIX . 'SMS_SENDER',
            Loc::getMessage($CONFIG_SUFFIX . "SMS_SENDER"),
            Option::get($MODULE_ID, $CONFIG_SUFFIX . "SMS_SENDER", ""),
            ['text'],
        ],
        [
            $CONFIG_SUFFIX . 'SMS_SENDER_TELEGRAM',
            Loc::getMessage($CONFIG_SUFFIX . "SMS_SENDER_TELEGRAM"),
            Option::get($MODULE_ID, $CONFIG_SUFFIX . "SMS_SENDER_TELEGRAM", ""),
            ['text'],
        ],
    ];

    $aTabs[] = [
        'DIV' => 'sms_settings',
        'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_SMS"),
        'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_SMS"),
        'OPTIONS' => $arFields['sms']
    ];
}

$arFields['vmi'] = [];

$vmiIntroductoryText = Option::get($MODULE_ID, $CONFIG_SUFFIX . "VMI_INTRODUCTORY_TEXT", "");
$vmiServiceDescription = Option::get($MODULE_ID, $CONFIG_SUFFIX . "VMI_SERVICE_DESCRIPTION", "");

$mediabankResponseUser=[];
$preMediabankResponseUser = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "MEDIABANK_RESPONSE_USER", ""), true);
if (is_array($preMediabankResponseUser)) {
    $mediabankResponseUser = $preMediabankResponseUser;
}

$aTabs[] = [
    'DIV' => 'vmi_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_VMI"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_VMI"),
    'OPTIONS' => $arFields['vmi']
];


$arFields['reminder'] = [];

$aTabs[] = [
    'DIV' => 'reminder_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_REMINDER"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_REMINDER"),
    'OPTIONS' => $arFields['reminder']
];

$arFields['mediabank'] = [
    [
        $CONFIG_SUFFIX . 'MEDIABANK_INFO_FALE_NAME',
        Loc::getMessage($CONFIG_SUFFIX . "MEDIABANK_INFO_FALE_NAME"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "MEDIABANK_INFO_FALE_NAME", ""),
        ['limitedText'],
        65535,
    ],
    [
        $CONFIG_SUFFIX . 'MEDIABANK_DISK_ID',
        Loc::getMessage($CONFIG_SUFFIX . "MEDIABANK_DISK_ID"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "MEDIABANK_DISK_ID", ""),
        ['text'],
    ],
];

$aTabs[] = [
    'DIV' => 'mediabank_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_MEDIABANK"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_MEDIABANK"),
    'OPTIONS' => $arFields['mediabank']
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

        $directionDeps = $request->getPost($CONFIG_SUFFIX . "DIRECTION_DEPS");
        if (is_array($directionDeps)) {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "DIRECTION_DEPS", json_encode($directionDeps));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "DIRECTION_DEPS", json_encode([]));
        }

        $directionDeps = $request->getPost($CONFIG_SUFFIX . "COMPANY_DEPS");
        if (is_array($directionDeps)) {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "COMPANY_DEPS", json_encode($directionDeps));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "COMPANY_DEPS", json_encode([]));
        }

        $directionDeps = $request->getPost($CONFIG_SUFFIX . "AREA_DEPS");
        if (is_array($directionDeps)) {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "AREA_DEPS", json_encode($directionDeps));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "AREA_DEPS", json_encode([]));
        }

        $selectedFilesPost = $request->getPost($CONFIG_SUFFIX . "REMINDER_WELCOME_VIDEO_TWO");
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
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "REMINDER_WELCOME_VIDEO_TWO", json_encode($selectedFilesPost));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "REMINDER_WELCOME_VIDEO_TWO", json_encode([]));
        }

        $selectedFilesPost = $request->getPost($CONFIG_SUFFIX . "REMINDER_WELCOME_VIDEO");
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
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "REMINDER_WELCOME_VIDEO", json_encode($selectedFilesPost));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "REMINDER_WELCOME_VIDEO", json_encode([]));
        }

        $welcomeText = $request->getPost($CONFIG_SUFFIX . "REMINDER_WELCOME_TEXT");
        if (!empty($welcomeText)) {
            $sanitizer = new \CBXSanitizer();
            $sanitizer->setLevel(\CBXSanitizer::SECURE_LEVEL_LOW);
            $sanitizer->ApplyDoubleEncode(false);
            $welcomeText = $sanitizer->SanitizeHtml($welcomeText);
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "REMINDER_WELCOME_TEXT", $welcomeText);
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "REMINDER_WELCOME_TEXT", '');
        }

        $selectedVmiStaffSchedulePost = $request->getPost($CONFIG_SUFFIX . "VMI_STAFF_SCHEDULE");
        Option::set($MODULE_ID, $CONFIG_SUFFIX . "VMI_STAFF_SCHEDULE", $selectedVmiStaffSchedulePost);

        $vmiIntroductoryText = $request->getPost($CONFIG_SUFFIX . "VMI_INTRODUCTORY_TEXT");
        if (!empty($vmiIntroductoryText)) {
            $sanitizer = new \CBXSanitizer();
            $sanitizer->setLevel(\CBXSanitizer::SECURE_LEVEL_LOW);
            $sanitizer->ApplyDoubleEncode(false);
            $vmiIntroductoryText = $sanitizer->SanitizeHtml($vmiIntroductoryText);
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "VMI_INTRODUCTORY_TEXT", $vmiIntroductoryText);
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "VMI_INTRODUCTORY_TEXT", '');
        }

        $vmiServiceDescription = $request->getPost($CONFIG_SUFFIX . "VMI_SERVICE_DESCRIPTION");
        if (!empty($vmiServiceDescription)) {
            $sanitizer = new \CBXSanitizer();
            $sanitizer->setLevel(\CBXSanitizer::SECURE_LEVEL_LOW);
            $sanitizer->ApplyDoubleEncode(false);
            $vmiServiceDescription = $sanitizer->SanitizeHtml($vmiServiceDescription);
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "VMI_SERVICE_DESCRIPTION", $vmiServiceDescription);
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "VMI_SERVICE_DESCRIPTION", '');
        }

        $mediabankResponseUser = $request->getPost($CONFIG_SUFFIX . "MEDIABANK_RESPONSE_USER");
        if (is_array($mediabankResponseUser)) {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "MEDIABANK_RESPONSE_USER",json_encode($mediabankResponseUser));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "MEDIABANK_RESPONSE_USER", json_encode([]));
        }

        \Mcart\Iblock\Departments\Help::updateSort();

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
            $tabControl->beginNextTab();

            if ($aTab['OPTIONS']) {
                __AdmSettingsDrawList($MODULE_ID, $aTab['OPTIONS']);
            }

        if ($aTab['DIV'] === 'deps_settings') {
            ?>
            <tr class="heading">
                <td colspan="2" align="center">
                    Дирекции
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <select size="8" name="<?= $CONFIG_SUFFIX ?>DIRECTION_DEPS[]" style="width:360px;" multiple="">
                        <?php
                        foreach ($deps as $depId => $depName) { ?>
                            <option value="<?= $depId ?>" title="<?= $depName ?>" <?= in_array(
                                $depId,
                                $selectedDeps
                            ) ? 'selected' : '' ?>> <?= $depName ?> </option>
                            <?php
                        } ?>
                    </select>
                </td>
            </tr>
            <script>
                $(document).ready(function () {
                    $('select[name="<?= $CONFIG_SUFFIX ?>DIRECTION_DEPS[]"]').select2();
                });
            </script>


            <tr class="heading">
                <td colspan="2" align="center">
                    Компании
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <select size="8" name="<?= $CONFIG_SUFFIX ?>COMPANY_DEPS[]" style="width:360px;" multiple="">
                        <?php
                        foreach ($deps as $depId => $depName) { ?>
                            <option value="<?= $depId ?>" title="<?= $depName ?>" <?= in_array(
                                $depId,
                                $selectedComps
                            ) ? 'selected' : '' ?>> <?= $depName ?> </option>
                            <?php
                        } ?>
                    </select>
                </td>
            </tr>
            <script>
                $(document).ready(function () {
                    $('select[name="<?= $CONFIG_SUFFIX ?>COMPANY_DEPS[]"]').select2();
                });
            </script>


            <tr class="heading">
                <td colspan="2" align="center">
                    Площадки
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <select size="8" name="<?= $CONFIG_SUFFIX ?>AREA_DEPS[]" style="width:360px;" multiple="">
                        <?php
                        foreach ($deps as $depId => $depName) { ?>
                            <option value="<?= $depId ?>" title="<?= $depName ?>" <?= in_array(
                                $depId,
                                $selectedAreas
                            ) ? 'selected' : '' ?>> <?= $depName ?> </option>
                            <?php
                        } ?>
                    </select>
                </td>
            </tr>
            <script>
                $(document).ready(function () {
                    $('select[name="<?= $CONFIG_SUFFIX ?>AREA_DEPS[]"]').select2();
                });
            </script>
        <?php
        }
        if ($aTab['DIV'] === 'reminder_settings'){
        ?>
            <tr class="heading" id="tr_PREVIEW_TEXT_LABEL">
                <td colspan="2">Приветственный текст</td>
            </tr>
            <tr id="tr_PREVIEW_TEXT_EDITOR">
                <td colspan="2" align="center">
                    <?
                    CFileMan::AddHTMLEditorFrame(
                        $CONFIG_SUFFIX . "REMINDER_WELCOME_TEXT",
                        $welcomeText,
                        "PREVIEW_TEXT_TYPE",
                        'html',
                        array(
                            'height' => 450,
                            'width' => '100%'
                        ),
                        "N",
                        0,
                        "",
                        "",
                        "",
                        true,
                        false,
                        array(
                            'toolbarConfig' => CFileMan::GetEditorToolbarConfig('admin'),
                            'saveEditorKey' => '',
                            'hideTypeSelector' => '',
                        )
                    ); ?>
                </td>
            </tr>

            <tr>
                <td width="40%" class="adm-detail-valign-top"><?= Loc::getMessage(
                        $CONFIG_SUFFIX . 'REMINDER_WELCOME_VIDEO'
                    ) ?></td>
                <td width="60%">
                    <?
                    echo \Bitrix\Main\UI\FileInput::createInstance(array(
                        "name" => $CONFIG_SUFFIX . 'REMINDER_WELCOME_VIDEO',
                        "description" => true,
                        "upload" => true,
                        "allowUpload" => "F",
                        "medialib" => true,
                        "fileDialog" => true,
                        "cloud" => true,
                        "delete" => true,
                        "maxCount" => 1
                    ))->show(
                        $selectedWelcomeFiles['fileId'] ? $selectedWelcomeFiles['fileId'] : ($selectedWelcomeFiles['prevFileId'] ? $selectedWelcomeFiles['prevFileId'] : 0)
                    );
                    ?>
                    <?
                    if (!empty($selectedWelcomeFiles['fileId']) || !empty($selectedWelcomeFiles['prevFileId'])): ?>
                        <input type="hidden" class="oldVideo"
                               name="<?= $CONFIG_SUFFIX ?>REMINDER_WELCOME_VIDEO[prevFileId]"
                               value="<?= $selectedWelcomeFiles['fileId'] ? $selectedWelcomeFiles['fileId'] : $selectedWelcomeFiles['prevFileId'] ?>">
                    <?
                    endif; ?>
                </td>
            </tr>

            <tr>
                <td width="40%" class="adm-detail-valign-top"><?= Loc::getMessage(
                        $CONFIG_SUFFIX . 'REMINDER_WELCOME_VIDEO_TWO'
                    ) ?></td>
                <td width="60%">
                    <?
                    echo \Bitrix\Main\UI\FileInput::createInstance(array(
                        "name" => $CONFIG_SUFFIX . 'REMINDER_WELCOME_VIDEO_TWO',
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
                    <?
                    if (!empty($selectedFiles['fileId']) || !empty($selectedFiles['prevFileId'])): ?>
                        <input type="hidden" class="oldVideo"
                               name="<?= $CONFIG_SUFFIX ?>REMINDER_WELCOME_VIDEO_TWO[prevFileId]"
                               value="<?= $selectedFiles['fileId'] ? $selectedFiles['fileId'] : $selectedFiles['prevFileId'] ?>">
                    <?
                    endif; ?>
                </td>
            </tr>
            <script>
                $('.adm-btn-del').click(function (event) {
                    event.target.closest('td').querySelector('.oldVideo').value = "";
                });
            </script>
        <?
        }


        if ($aTab['DIV'] === 'vmi_settings') {
        ?>
            <tr class="heading" id="tr_PREVIEW_TEXT_LABEL">
                <td colspan="2">Вводный текст</td>
            </tr>
            <tr id="tr_PREVIEW_TEXT_EDITOR">
                <td colspan="2" align="center">
                    <?
                    CFileMan::AddHTMLEditorFrame(
                        $CONFIG_SUFFIX . "VMI_INTRODUCTORY_TEXT",
                        $vmiIntroductoryText,
                        "PREVIEW_TEXT_TYPE",
                        'html',
                        array(
                            'height' => 450,
                            'width' => '100%'
                        ),
                        "N",
                        0,
                        "",
                        "",
                        "",
                        true,
                        false,
                        array(
                            'toolbarConfig' => CFileMan::GetEditorToolbarConfig('admin'),
                            'saveEditorKey' => '',
                            'hideTypeSelector' => '',
                        )
                    ); ?>
                </td>
            </tr>
            <tr class="heading" id="tr_PREVIEW_TEXT_LABEL">
                <td colspan="2">Описание услуги</td>
            </tr>
            <tr id="tr_PREVIEW_TEXT_EDITOR">
                <td colspan="2" align="center">
                    <?
                    CFileMan::AddHTMLEditorFrame(
                        $CONFIG_SUFFIX . "VMI_SERVICE_DESCRIPTION",
                        $vmiServiceDescription,
                        "PREVIEW_TEXT_TYPE",
                        'html',
                        array(
                            'height' => 450,
                            'width' => '100%'
                        ),
                        "N",
                        0,
                        "",
                        "",
                        "",
                        true,
                        false,
                        array(
                            'toolbarConfig' => CFileMan::GetEditorToolbarConfig('admin'),
                            'saveEditorKey' => '',
                            'hideTypeSelector' => '',
                        )
                    ); ?>
                </td>
            </tr>
            <?
        if (\Bitrix\Main\Loader::includeModule("highloadblock")) {
            $tableNameHL = array('staff_schedule');
            $arVmiStaffSchedule = array(
                'REFERENCE' => array('Выбрать штатное расписание'),
                'REFERENCE_ID' => array('0')
            );


            $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
                array('filter' => array('=TABLE_NAME' => $tableNameHL))
            )->fetch();
            $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $dbHlData = $entity_data_class::getList(array(
                'select' => array('ID', 'UF_NAME'),
                'order' => array('ID' => 'ASC'),
            ));
            while ($arHlData = $dbHlData->fetch()) {
                $arVmiStaffSchedule['REFERENCE'][] = $arHlData['UF_NAME'];
                $arVmiStaffSchedule['REFERENCE_ID'][] = $arHlData['ID'];
            }

            ?>
            <tr>
                <td class="adm-detail-content-cell-l"><?= Loc::getMessage($CONFIG_SUFFIX . 'VMI_STAFF_SCHEDULE') ?></td>
                <td class="adm-detail-content-cell-r"><?= SelectBoxFromArray(
                        $CONFIG_SUFFIX . 'VMI_STAFF_SCHEDULE',
                        $arVmiStaffSchedule,
                        $selectedVmiStaffSchedule
                    ); ?></td>
            </tr>
            <?
        }
        }

        if ($aTab['DIV'] === 'mediabank_settings') {?>
            <tr>
                <td  width="50%" class="adm-detail-content-cell-l">
                    <?=Loc::getMessage($CONFIG_SUFFIX . "MEDIABANK_RESPONSE_USER")?>
                </td>
                <td  width="20%" class="adm-detail-content-cell-l">
                    <?
                    $GLOBALS["APPLICATION"]->IncludeComponent('bitrix:intranet.user.selector', '', array(
                            'INPUT_NAME' =>$CONFIG_SUFFIX."MEDIABANK_RESPONSE_USER",
                            "MULTIPLE" => "N",
                            'INPUT_NAME_STRING' => "shipped_contact_h",
                            'INPUT_NAME_SUSPICIOUS' => "shipped_contact_h",
                            'TEXTAREA_MIN_HEIGHT' => 30,
                            'TEXTAREA_MAX_HEIGHT' => 60,
                            'INPUT_VALUE' => $mediabankResponseUser?$mediabankResponseUser:"",
                            'EXTERNAL' => 'A',
                            'SOCNET_GROUP_ID' => ""
                        )
                    );
                    ?>
                </td>
            </tr>
        <?}

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