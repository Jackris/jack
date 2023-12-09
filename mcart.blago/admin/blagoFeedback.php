<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/prolog.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpApplication;
CUtil::InitJSCore(array("jquery","date"));
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

$PAGE_ID = "blagoFeedback";
$MODULE_ID = "mcart.blago";

$MODULE_SUFFIX = "MCART_BLAGO";
$CONFIG_SUFFIX = $MODULE_SUFFIX . "_FEEDBACK_";

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

$arFields['delegate_user'] = [
    [
        $CONFIG_SUFFIX . 'FORUM_ID',
        Loc::getMessage($CONFIG_SUFFIX . "FORUM_ID"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "FORUM_ID", ""),
        ['number'],
        4,
    ],
];

$aTabs[] = [
    'DIV' => 'delegate_user_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_DELEGATE_USER"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_DELEGATE_USER"),
    'OPTIONS' => $arFields['delegate_user']
];
$vmiServiceDescription = Option::get($MODULE_ID, $CONFIG_SUFFIX . "VMI_SERVICE_DESCRIPTION", "");

$eventTitle = Option::get($MODULE_ID, $CONFIG_SUFFIX . "EVENT_TITLE", "");
$eventText = Option::get($MODULE_ID, $CONFIG_SUFFIX . "EVENT_TEXT", "");
$eventText2 = Option::get($MODULE_ID, $CONFIG_SUFFIX . "EVENT_TEXT2", "");

$modalTitle = Option::get($MODULE_ID, $CONFIG_SUFFIX . "MODAL_TITLE", "");
$modalText = Option::get($MODULE_ID, $CONFIG_SUFFIX . "MODAL_TEXT", "");



$delegateEmployee=[];
$preDelegateEmployee = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "DELEGATE_EMP", ""), true);
if (!empty($preDelegateEmployee)) {
    $delegateEmployee[] = $preDelegateEmployee;
}

$positions=[];
$prePositions = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "POSITIONS", ""), true);
if (!empty($prePositions)) {
    $positions = $prePositions;
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
        $directionDeps = $request->getPost($CONFIG_SUFFIX . "DELEGATE_EMP");
        if (!empty($directionDeps)) {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "DELEGATE_EMP",json_encode($directionDeps));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "DELEGATE_EMP", json_encode([]));
        }

        $directionDeps = $request->getPost($CONFIG_SUFFIX . "POSITIONS");
        if (!empty($directionDeps)) {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "POSITIONS",json_encode($directionDeps));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "POSITIONS", json_encode([]));
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

        $eventTitle = $request->getPost($CONFIG_SUFFIX . "EVENT_TITLE");
        if (!empty($eventTitle)){
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "EVENT_TITLE", $eventTitle);
        }else{
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "EVENT_TITLE", '');
        }
        $eventText = $request->getPost($CONFIG_SUFFIX . "EVENT_TEXT");
        if (!empty($eventText)){
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "EVENT_TEXT", $eventText);
        }else{
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "EVENT_TEXT", '');
        }
        $eventText2 = $request->getPost($CONFIG_SUFFIX . "EVENT_TEXT2");
        if (!empty($eventText2)){
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "EVENT_TEXT2", $eventText2);
        }else{
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "EVENT_TEXT2", '');
        }

        $modalTitle = $request->getPost($CONFIG_SUFFIX . "MODAL_TITLE");
        if (!empty($modalTitle)){
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "MODAL_TITLE", $modalTitle);
        }else{
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "MODAL_TITLE", '');
        }
        $modalText = $request->getPost($CONFIG_SUFFIX . "MODAL_TEXT");
        if (!empty($modalText)){
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "MODAL_TEXT", $modalText);
        }else{
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "MODAL_TEXT", '');
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
            if ($aTab['DIV'] === 'delegate_user_settings') {
                ?>
                <tr>
                    <td  width="50%" class="adm-detail-content-cell-l">
                        Делегирующие сотрудники:
                    </td>
                    <td  width="50%" class="adm-detail-content-cell-l">
                        <?
                        $GLOBALS["APPLICATION"]->IncludeComponent(
                            'bitrix:main.user.selector',
                            ' ',
                            [
                                'LIST' => $delegateEmployee ? $delegateEmployee : [],
                                "API_VERSION" => 3,
                                "INPUT_NAME" => $CONFIG_SUFFIX."DELEGATE_EMP",
                                "USE_SYMBOLIC_ID" => false,
                                "BUTTON_SELECT_CAPTION" => Loc::getMessage("MAIL_CLIENT_CONFIG_CRM_QUEUE_ADD"),
                                "SELECTOR_OPTIONS" =>
                                    [
                                        "departmentSelectDisable" => "Y",
                                        'context' => 'MAIL_CLIENT_CONFIG_QUEUE',
                                        'contextCode' => '',
                                        'enableAll' => 'N',
                                        'userSearchArea' => 'I'
                                    ]
                            ]
                        );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td  width="50%" class="adm-detail-content-cell-l">
                        Контролирующие должности:
                    </td>
                    <td  width="50%" class="adm-detail-content-cell-l" id="test123">
                        <div class="input-wrapper input-wrapper--select">
                            <span class="floating-label">Ответственный</span>
                            <div class="select-container">
                                <select name="author" class="mmf-modal__select multiple-select m-select-multiple " multiple="multiple">
                                    <?php if(!empty($positions)):?>
                                        <?php foreach ($positions as $key=>$position):?>
                                            <option value="<?=$key?>" selected> <?=$position?></option>
                                        <?php endforeach;?>
                                    <?php endif;?>
                                </select>
                            </div>
                        </div>
                        <div id="hidden__inputs">
                            <?php if(!empty($positions)):?>
                                <?php foreach ($positions as $key=>$position):?>
                                    <input type="hidden" name="<?=$CONFIG_SUFFIX?>POSITIONS[<?=$key?>]" value="<?=$position?>"> </input>
                                <?php endforeach;?>
                            <?php endif;?>
                        </div>
<!--                        <input type="hidden" name="--><?php //=$CONFIG_SUFFIX?><!--DELEGATE_EMP[]">-->
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
        <tr class="heading">
            <td colspan="2">Уведомление</td>
        </tr>
        <tr>
            <td >Заголовок</td>
            <td>
                <input type="text" value="<?=$eventTitle?>" name="<?=$CONFIG_SUFFIX?>EVENT_TITLE">
            </td>
        </tr>
        <tr>
            <td>текст</td>
            <td>
                <input type="text" value="<?=$eventText?>" name="<?=$CONFIG_SUFFIX?>EVENT_TEXT">
            </td>
        </tr>
        <tr>
            <td>текст</td>
            <td>
                <input type="text" value="<?=$eventText2?>" name="<?=$CONFIG_SUFFIX?>EVENT_TEXT2">
            </td>
        </tr>
        <tr class="heading">
            <td colspan="2">Модалка</td>
        </tr>
        <tr>
            <td >Заголовок</td>
            <td>
                <input type="text" value="<?=$modalTitle?>" name="<?=$CONFIG_SUFFIX?>MODAL_TITLE">
            </td>
        </tr>
        <tr>
            <td>текст</td>
            <td>
                <input type="text" value="<?=$modalText?>" name="<?=$CONFIG_SUFFIX?>MODAL_TEXT">
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
    <script>
        let select=  $('select[name="author"]');
            var Utils = $.fn.select2.amd.require('select2/utils');
            var Dropdown = $.fn.select2.amd.require('select2/dropdown');
            var DropdownSearch = $.fn.select2.amd.require('select2/dropdown/search');
            var CloseOnSelect = $.fn.select2.amd.require('select2/dropdown/closeOnSelect');
            var AttachBody = $.fn.select2.amd.require('select2/dropdown/attachBody');

            var dropdownAdapter = Utils.Decorate(Utils.Decorate(Utils.Decorate(Dropdown, DropdownSearch), CloseOnSelect), AttachBody);

            select.select2({
                dropdownAdapter: dropdownAdapter,
                minimumResultsForSearch: 0,
                placeholder: ' ',
                dropdownCssClass: ':all:',
                selectionCssClass: ':all:',
                templateResult: this.selectUserForm,
                allowClear: true,
                language: {
                    noResults: () => "Ничего не найдено",
                    searching: () => "Поиск...",
                    loadingMore: () => "Загружаем еще..."
                },
                ajax: {
                    url: "/bitrix/services/main/ajax.php?mode=class&c=mcart:feedback.create&action=getPositions",
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            sessid: BX.bitrix_sessid(),
                            search: params.term,
                            page: params.page || 1,
                            ids: []
                        }
                        if (Array.isArray(this.data('select2').data())) {
                            this.data('select2').data().forEach((user) => {
                                query['ids'].push(user.id);
                            });
                        }
                        return query;
                    },
                    processResults: function (result) {
                        console.log(result);
                        return result.data;
                    }
                }
            });
            select.on('change', BX.proxy(onChangeListSelect, this));
            function onChangeListSelect(event){
                let selected = document.querySelectorAll('select[name="author"] option:checked');
                let container= document.querySelector('#hidden__inputs');
                container.innerHTML='';
                var i;
                for (i = 0; i < selected.length; ++i) {
                    container.append( BX.create('INPUT', {props:{type:'hidden',name:'MCART_BLAGO_FEEDBACK_POSITIONS['+selected[i].value+']',value:selected[i].text}}));
                }
             //let values = Array.from(selected).map(el => el.value);
             //let names = Array.from(selected).map(el => el.text);
            }
            //select.on('select2:open', BX.proxy(this.onOpenListSelect, this));
           // select.on('select2:close', BX.proxy(this.onCloseListSelect, this));
        $(".ss").keypress(function(event){
            event = event || window.event;
            if (event.charCode && event.charCode!=0 && event.charCode!=44 && (event.charCode < 48 || event.charCode > 57) )
                return false;
        });
    </script>
<?php
$tabControl->end();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
