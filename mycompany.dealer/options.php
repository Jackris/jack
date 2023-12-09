<?

use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Socialnetwork\WorkgroupTable;
use Mcart\Exchange\McartHelper;
use Bitrix\Main\HttpApplication;
use \CSecurityUser;


global $MESS;
Loc::loadMessages(__FILE__);
Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/options.php');

$MODULE_ID = "mycompany.dealer";
$MOD_RIGHT = $APPLICATION->GetGroupRight($MODULE_ID);
$request = HttpApplication::getInstance()->getContext()->getRequest();

if ($MOD_RIGHT >= 'Y' || $USER->IsAdmin()) {

    $aTabs = [
        array(
            'DIV' => 'tab1',
            'TAB' => 'Дилеры',
            'TITLE' => 'Максимальное количества моделей машин, которые может продавать один дилер',
            'OPTIONS' => array(
                array(
                    'max_count_models',
                    GetMessage('MCART_SEARCH_TAB_1_length_word'),
                    Option::get($MODULE_ID, "max_count_models", 5),
                    array('text'),
                ),
            )
        ),
    ];

    /*
     * Создаем форму для редактирвания параметров модуля
     */
    $tabControl = new CAdminTabControl(
        'tabControl',
        $aTabs
    );
    $tabControl->begin();
    ?>
    <form action="<?= $APPLICATION->getCurPage(); ?>?mid=<?=$MODULE_ID; ?>&lang=<?= LANGUAGE_ID; ?>" method="post">
        <?= bitrix_sessid_post(); ?>
        <?php foreach ($aTabs as $aTab) {
            if ($aTab['OPTIONS']) {
                $tabControl->beginNextTab();
                __AdmSettingsDrawList($MODULE_ID, $aTab['OPTIONS']);
            }
        }
        $tabControl->buttons();
        ?>
        <input type="submit" name="apply" value="Сохранить" class="adm-btn-save" />
        <input type="submit" name="default" value="Отмена" />
    </form>
    <?php
    $tabControl->end();

    /*
     * Обрабатываем данные после отправки формы
     */
    if ($request->isPost() && check_bitrix_sessid()) {
        foreach ($aTabs as $aTab) { // цикл по вкладкам
            foreach ($aTab['OPTIONS'] as $arOption) {
                if (!is_array($arOption)) { // если это название секции
                    continue;
                }
                if ($arOption['note']) { // если это примечание
                    continue;
                }

                if ($request['apply']) { // сохраняем введенные настройки
                    $optionValue = $request->getPost($arOption[0]);

                    Option::set($MODULE_ID, $arOption[0], is_array($optionValue) ? implode(',', $optionValue) : $optionValue);

                } elseif ($request['default']) { // устанавливаем по умолчанию
                    Option::set($MODULE_ID, $arOption[0], '');
                }
            }
        }
        LocalRedirect($APPLICATION->getCurPage() . '?mid=' . $MODULE_ID . '&lang=' . LANGUAGE_ID);
    }
}
?>