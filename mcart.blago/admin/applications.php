<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/modules/mcart.blago/prolog.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpApplication;
use Bitrix\Highloadblock\HighloadBlockTable;

IncludeModuleLangFile(__FILE__);

CUtil::InitJSCore(array("jquery", "date"));
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

global $APPLICATION;

$PAGE_ID = "applications";
$MODULE_ID = "mcart.blago";

$MODULE_SUFFIX = "MCART_BLAGO";
$CONFIG_SUFFIX = $MODULE_SUFFIX . "_APPLICATIONS_";

if (!Loader::includeModule($MODULE_ID)) {
    ?>
    <span class="required">
            <?= Loc::getMessage($CONFIG_SUFFIX . 'INCLUDE_MODULE_ERROR', ['#NAME#' => $MODULE_ID]) ?>
        </span>
    <?php
    die;
}

if (!Loader::includeModule('highloadblock')) {
    ?>
    <span class="required">
            <?= Loc::getMessage($CONFIG_SUFFIX . 'INCLUDE_MODULE_ERROR', ['#NAME#' => 'highloadblock']) ?>
        </span>
    <?php
    die;
}

if ($APPLICATION->GetGroupRight($MODULE_ID) === 'D') {
    $APPLICATION->AuthForm(Loc::getMessage($CONFIG_SUFFIX . 'ACCESS_DENIED'));
}

$APPLICATION->SetTitle(GetMessage($CONFIG_SUFFIX . 'TITLE'));

$ob = HighloadBlockTable::getList([
    'select' => ['ID'],
    'order' => ['ID' => 'ASC'],
    'filter' => [
        '=TABLE_NAME' => 'legal_entities',
    ],
    'limit' => 1,
]);

$legal_hl_block_id = 0;
if ($row = $ob->fetch()) {
    $legal_hl_block_id = $row['ID'];
}

$legals = [];
$hl = HighloadBlockTable::getById($legal_hl_block_id)->fetch();
$dataClass = HighloadBlockTable::compileEntity($hl)->getDataClass();
$ob = $dataClass::query()
    ->setSelect(['*'])
    ->exec();
while ($row = $ob->fetch()) {
    $legals[$row['ID']] = [
        'NAME' => $row['UF_NAME'],
        'VALUE_RESP' => [],
        'VALUE_HEAD' => []
    ];

    $preHrRespPosition = json_decode(
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "HR_RESP_POSITION_" . $row['ID'], ""),
        true
    );
    if (!empty($preHrRespPosition)) {
        $legals[$row['ID']]['VALUE_RESP'] = $preHrRespPosition;
    }

    $preHrHeadPosition = json_decode(
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "HR_HEAD_POSITION_" . $row['ID'], ""),
        true
    );
    if (!empty($preHrHeadPosition)) {
        $legals[$row['ID']]['VALUE_HEAD'] = $preHrHeadPosition;
    }
}


$ob = HighloadBlockTable::getList([
    'select' => ['ID'],
    'order' => ['ID' => 'ASC'],
    'filter' => [
        '=TABLE_NAME' => 'application_reminders',
    ],
    'limit' => 1,
]);

$reminders_hl_block_id = 0;
if ($row = $ob->fetch()) {
    $reminders_hl_block_id = $row['ID'];
}

$aTabs = [];
$arFields = [];

$arFields['buh'] = [];

$arFields['hr'] = [];

$arFields['reminders'] = [
    ['note' => Loc::getMessage($CONFIG_SUFFIX . 'LINK_REMINDERS', ['#ID#' => $reminders_hl_block_id])],
];


$aTabs[] = [
    'DIV' => 'buh_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_BUH"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_BUH"),
    'OPTIONS' => $arFields['buh']
];

$aTabs[] = [
    'DIV' => 'hr_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_HR"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_HR"),
    'OPTIONS' => $arFields['hr']
];

$aTabs[] = [
    'DIV' => 'reminders_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_REMINDERS"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_REMINDERS"),
    'OPTIONS' => $arFields['reminders']
];

$buhHeadPosition = [];
$preBuhHeadPosition = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "BUH_HEAD_POSITION", ""), true);
if (!empty($preBuhHeadPosition)) {
    $buhHeadPosition = $preBuhHeadPosition;
}

$buhLeadPosition = [];
$preBuhLeadPosition = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "BUH_LEAD_POSITION", ""), true);
if (!empty($preBuhLeadPosition)) {
    $buhLeadPosition = $preBuhLeadPosition;
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

        $buhHeadPosition = $request->getPost($CONFIG_SUFFIX . "BUH_HEAD_POSITION");
        if (!empty($buhHeadPosition)) {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "BUH_HEAD_POSITION", json_encode($buhHeadPosition));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "BUH_HEAD_POSITION", json_encode([]));
        }

        $buhLeadPosition = $request->getPost($CONFIG_SUFFIX . "BUH_LEAD_POSITION");
        if (!empty($buhLeadPosition)) {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "BUH_LEAD_POSITION", json_encode($buhLeadPosition));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "BUH_LEAD_POSITION", json_encode([]));
        }

        foreach ($legals as $legalId => $legal) {
            $hrHeadPosition = $request->getPost($CONFIG_SUFFIX . "HR_HEAD_POSITION_" . $legalId);
            if (!empty($hrHeadPosition)) {
                Option::set($MODULE_ID, $CONFIG_SUFFIX . "HR_HEAD_POSITION_" . $legalId, json_encode($hrHeadPosition));
            } else {
                Option::set($MODULE_ID, $CONFIG_SUFFIX . "HR_HEAD_POSITION_" . $legalId, json_encode([]));
            }

            $hrRespPosition = $request->getPost($CONFIG_SUFFIX . "HR_RESP_POSITION_" . $legalId);
            if (!empty($hrRespPosition)) {
                Option::set($MODULE_ID, $CONFIG_SUFFIX . "HR_RESP_POSITION_" . $legalId, json_encode($hrRespPosition));
            } else {
                Option::set($MODULE_ID, $CONFIG_SUFFIX . "HR_RESP_POSITION_" . $legalId, json_encode([]));
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
            $tabControl->beginNextTab();

            if ($aTab['OPTIONS']) {
                __AdmSettingsDrawList($MODULE_ID, $aTab['OPTIONS']);
            }

            if ($aTab['DIV'] === 'buh_settings') {
                ?>
                <tr>
                    <td width="50%" class="adm-detail-content-cell-l">
                        <?= Loc::getMessage($CONFIG_SUFFIX . "BUH_HEAD_POSITION") ?>
                    </td>
                    <td width="50%" class="adm-detail-content-cell-r" id="buh_head_position_id">
                        <div class="input-wrapper input-wrapper--select">
                            <div class="select-container">
                                <select name="buh_head_position" class="mmf-modal__select"
                                        data-name="<?= $CONFIG_SUFFIX ?>BUH_HEAD_POSITION">
                                    <?php
                                    if (!empty($buhHeadPosition)): ?>
                                        <?php
                                        foreach ($buhHeadPosition as $key => $position): ?>
                                            <option value="<?= $key ?>" selected> <?= $position ?></option>
                                        <?php
                                        endforeach; ?>
                                    <?php
                                    endif; ?>
                                </select>
                            </div>
                        </div>
                        <div id="hidden__inputs">
                            <?php
                            if (!empty($buhHeadPosition)): ?>
                                <?php
                                foreach ($buhHeadPosition as $key => $position): ?>
                                    <input type="hidden" name="<?= $CONFIG_SUFFIX ?>BUH_HEAD_POSITION[<?= $key ?>]"
                                           value="<?= $position ?>"> </input>
                                <?php
                                endforeach; ?>
                            <?php
                            endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="50%" class="adm-detail-content-cell-l">
                        <?= Loc::getMessage($CONFIG_SUFFIX . "BUH_LEAD_POSITION") ?>
                    </td>
                    <td width="50%" class="adm-detail-content-cell-r" id="buh_lead_position_id">
                        <div class="input-wrapper input-wrapper--select">
                            <div class="select-container">
                                <select name="buh_lead_position" class="mmf-modal__select"
                                        data-name="<?= $CONFIG_SUFFIX ?>BUH_LEAD_POSITION">
                                    <?php
                                    if (!empty($buhLeadPosition)): ?>
                                        <?php
                                        foreach ($buhLeadPosition as $key => $position): ?>
                                            <option value="<?= $key ?>" selected> <?= $position ?></option>
                                        <?php
                                        endforeach; ?>
                                    <?php
                                    endif; ?>
                                </select>
                            </div>
                        </div>
                        <div id="hidden__inputs">
                            <?php
                            if (!empty($buhLeadPosition)): ?>
                                <?php
                                foreach ($buhLeadPosition as $key => $position): ?>
                                    <input type="hidden" name="<?= $CONFIG_SUFFIX ?>BUH_LEAD_POSITION[<?= $key ?>]"
                                           value="<?= $position ?>"> </input>
                                <?php
                                endforeach; ?>
                            <?php
                            endif; ?>
                        </div>
                    </td>
                </tr>
                <?php
            }

            if ($aTab['DIV'] === 'hr_settings') {
                foreach ($legals as $legalId => $legal) {
                    ?>
                    <tr>
                        <td width="50%" class="adm-detail-content-cell-l">
                            <?= Loc::getMessage($CONFIG_SUFFIX . "HR_HEAD_POSITION", ['#LEGAL_NAME#' => $legal['NAME']]
                            ) ?>
                        </td>
                        <td width="50%" class="adm-detail-content-cell-r" id="hr_head_position_<?= $legalId ?>_id">
                            <div class="input-wrapper input-wrapper--select">
                                <div class="select-container">
                                    <select name="hr_head_position_<?= $legalId ?>" class="mmf-modal__select"
                                            data-name="<?= $CONFIG_SUFFIX ?>HR_HEAD_POSITION_<?= $legalId ?>">
                                        <?php
                                        if (!empty($legal['VALUE_HEAD'])): ?>
                                            <?php
                                            foreach ($legal['VALUE_HEAD'] as $key => $position): ?>
                                                <option value="<?= $key ?>" selected> <?= $position ?></option>
                                            <?php
                                            endforeach; ?>
                                        <?php
                                        endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div id="hidden__inputs">
                                <?php
                                if (!empty($legal['VALUE_HEAD'])): ?>
                                    <?php
                                    foreach ($legal['VALUE_HEAD'] as $key => $position): ?>
                                        <input type="hidden"
                                               name="<?= $CONFIG_SUFFIX ?>HR_HEAD_POSITION_<?= $legalId ?>[<?= $key ?>]"
                                               value="<?= $position ?>"> </input>
                                    <?php
                                    endforeach; ?>
                                <?php
                                endif; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" class="adm-detail-content-cell-l">
                            <?= Loc::getMessage($CONFIG_SUFFIX . "HR_RESP_POSITION", ['#LEGAL_NAME#' => $legal['NAME']]
                            ) ?>
                        </td>
                        <td width="50%" class="adm-detail-content-cell-r" id="hr_resp_position_<?= $legalId ?>_id">
                            <div class="input-wrapper input-wrapper--select">
                                <div class="select-container">
                                    <select name="hr_resp_position_<?= $legalId ?>" class="mmf-modal__select"
                                            data-name="<?= $CONFIG_SUFFIX ?>HR_RESP_POSITION_<?= $legalId ?>">
                                        <?php
                                        if (!empty($legal['VALUE_RESP'])): ?>
                                            <?php
                                            foreach ($legal['VALUE_RESP'] as $key => $position): ?>
                                                <option value="<?= $key ?>" selected> <?= $position ?></option>
                                            <?php
                                            endforeach; ?>
                                        <?php
                                        endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div id="hidden__inputs">
                                <?php
                                if (!empty($legal['VALUE_RESP'])): ?>
                                    <?php
                                    foreach ($legal['VALUE_RESP'] as $key => $position): ?>
                                        <input type="hidden"
                                               name="<?= $CONFIG_SUFFIX ?>HR_RESP_POSITION_<?= $legalId ?>[<?= $key ?>]"
                                               value="<?= $position ?>"> </input>
                                    <?php
                                    endforeach; ?>
                                <?php
                                endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            }
        } ?>
        <?php
        $tabControl->buttons(); ?>

        <input type="submit" name="Update"
               value="<?= Loc::getMessage($CONFIG_SUFFIX . 'SAVE') ?>"
               class="adm-btn-save"/>
    </form>
    <script>
        initPositionSelect('buh_head_position');
        initPositionSelect('buh_lead_position');
        <?php
        foreach ($legals as $legalId => $legal){
        ?>
        initPositionSelect('hr_head_position_<?= $legalId ?>');
        initPositionSelect('hr_resp_position_<?= $legalId ?>');
        <?php
        }
        ?>

        function initPositionSelect(selectName) {
            let select = $('select[name="' + selectName + '"]');

            let Utils = $.fn.select2.amd.require('select2/utils');
            let Dropdown = $.fn.select2.amd.require('select2/dropdown');
            let DropdownSearch = $.fn.select2.amd.require('select2/dropdown/search');
            let CloseOnSelect = $.fn.select2.amd.require('select2/dropdown/closeOnSelect');
            let AttachBody = $.fn.select2.amd.require('select2/dropdown/attachBody');

            let dropdownAdapter = Utils.Decorate(Utils.Decorate(Utils.Decorate(Dropdown, DropdownSearch), CloseOnSelect), AttachBody);

            select.select2({
                dropdownAdapter: dropdownAdapter,
                minimumResultsForSearch: 0,
                placeholder: ' ',
                dropdownCssClass: ':all:',
                selectionCssClass: ':all:',
                allowClear: true,
                language: {
                    noResults: () => "Ничего не найдено",
                    searching: () => "Поиск...",
                    loadingMore: () => "Загружаем еще..."
                },
                ajax: {
                    url: "/bitrix/services/main/ajax.php?action=mcart:blago.api.Help.getPositions",
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
                        return result.data;
                    }
                }
            });
            select.on('change', onChangePositionSelect);
        }

        function onChangePositionSelect(event) {
            if (!event.target) {
                return;
            }

            if (!event.target.name) {
                return;
            }

            let container = document.querySelector('#' + event.target.name + '_id');
            let selected = container.querySelectorAll('select[name="' + event.target.name + '"] option:checked');

            let values = container.querySelector('#hidden__inputs');
            values.innerHTML = '';

            let i;
            for (i = 0; i < selected.length; ++i) {
                values.append(BX.create('INPUT', {
                    props: {
                        type: 'hidden',
                        name: event.target.dataset.name + '[' + selected[i].value + ']',
                        value: selected[i].text
                    }
                }));
            }
        }
    </script>
<?php
$tabControl->end();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");