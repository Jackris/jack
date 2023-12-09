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
<script src="/local/components/mcart/company.today/templates/.default/js/jquery.maskedinput.js"/></script>
<style>
  a.add:before, a.addCulture:before{
    background: url("/bitrix/panel/main/images/bx-admin-sprite-small-2.png") 0 -2428px;
    content: "";
    display: inline-block;
    height: 17px;
    margin: 9px 9px 5px 9px;
    opacity: 1;
    position: static;
    vertical-align: center;
    width: 13px;
}
a.remove:before{
   
    background: url("/bitrix/panel/main/images/popup_menu_sprite_2.png") no-repeat 6px -1080px;
    background-position: 6px -780px;
    content: "";
    display: inline-block;
    height: 22px;
    margin: 0 4px 8px -7px;
    opacity: 1;
    position: static;
    vertical-align: center;
    width: 30px;
}
a.add, a.remove, a.addCulture {
    -webkit-border-radius: 4px;
    border-radius: 4px;
    border: none;
    /* border-top: 1px solid #fff; */
    -webkit-box-shadow: 0 0 1px rgba(0, 0, 0, .11), 0 1px 1px rgba(0, 0, 0, .3), inset 0 1px #fff, inset 0 0 1px rgba(255, 255, 255, .5);
    box-shadow: 0 0 1px rgba(0, 0, 0, .3), 0 1px 1px rgba(0, 0, 0, .3), inset 0 1px 0 #fff, inset 0 0 1px rgba(255, 255, 255, .5);
    background-color: #e0e9ec;
    background-image: -webkit-linear-gradient(bottom, #d7e3e7, #fff) !important;
    background-image: -moz-linear-gradient(bottom, #d7e3e7, #fff) !important;
    background-image: -ms-linear-gradient(bottom, #d7e3e7, #fff) !important;
    background-image: -o-linear-gradient(bottom, #d7e3e7, #fff) !important;
    background-image: linear-gradient(bottom, #d7e3e7, #fff) !important;
    color: #3f4b54;
    cursor: pointer;
    display: inline-block;
    font-family: var(--ui-font-family-primary, var(--ui-font-family-helvetica));
    font-weight: var(--ui-font-weight-bold);
    font-size: 13px;
    /* line-height: 18px; */
    height: 30px;
    width: 30px;
    text-shadow: 0 1px rgba(255, 255, 255, 0.7);
    text-decoration: none;
    position: relative;
    vertical-align: middle;
    -webkit-font-smoothing: antialiased;
}
     .bx-ius-layout{
        display: flex;
     }
     .ui-datepicker-calendar {
   display: none;
}
.ui-datepicker-month {
   display: none;
}
.ui-datepicker-next,.ui-datepicker-prev {
  display:none;
}

.annual__turnover{
display: flex;
align-items: center;
}
.adm-info-message-red{
display:none;
}
.red_error{
border: 1px solid red !important;
}
.tooltip {
      position: fixed;
      padding: 10px 20px;
      border: 1px solid #b3c9ce;
      border-radius: 4px;
      text-align: center;
      font: italic 14px/1.3 sans-serif;
      color: #333;
      background: #fff;
      box-shadow: 3px 3px 3px rgba(0, 0, 0, .3);
    }
</style>
'
);
IncludeModuleLangFile(__FILE__);

global $APPLICATION;

$PAGE_ID = "blagoToday";
$MODULE_ID = "mcart.blago";

$MODULE_SUFFIX = "MCART_BLAGO";
$CONFIG_SUFFIX = $MODULE_SUFFIX . "_BLAGO_TODAY_";

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

$arFields['employee_count'] = [
    [
        $CONFIG_SUFFIX . 'EMPLOYEE_COUNT',
        Loc::getMessage($CONFIG_SUFFIX . "EMPLOYEE_COUNT"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "EMPLOYEE_COUNT", ""),
        ['number'],
        4,
        'required'
    ],
    [
        $CONFIG_SUFFIX . 'NOTE',
        Loc::getMessage($CONFIG_SUFFIX . "NOTE"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "NOTE", ""),
        ['limitedText'],
        65535,
        'required'
    ],
];

$aTabs[] = [
    'DIV' => 'employee_count_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_EMPLOYEE_COUNT"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_EMPLOYEE_COUNT"),
    'OPTIONS' => $arFields['employee_count']
];

$arFields['annual_turnover'] = [
    [
//        $CONFIG_SUFFIX . 'YEAR',
//        Loc::getMessage($CONFIG_SUFFIX . "YEAR"),
//        Option::get($MODULE_ID, $CONFIG_SUFFIX . "YEAR", ""),
//        ['number'],
//        4,
//        'required'
    ],
//    [
//        $CONFIG_SUFFIX . 'TURNOVER',
//        Loc::getMessage($CONFIG_SUFFIX . "TURNOVER"),
//        Option::get($MODULE_ID, $CONFIG_SUFFIX . "TURNOVER", ""),
//        ['number'],
//        4,
//        'required'
//    ],
];
$aTabs[] = [
    'DIV' => 'annual_turnover_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_ANNUAL_TURNOVER"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_ANNUAL_TURNOVER"),
    'OPTIONS' => $arFields['annual_turnover']
];

$arFields['networks_for_sale'] = [
    [

    ],

];
$aTabs[] = [
    'DIV' => 'networks_for_sale_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_NETWORKS"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_NETWORKS"),
    'OPTIONS' => $arFields['networks_for_sale']
];

$arFields['quote'] = [
    [
        $CONFIG_SUFFIX . 'QUOTE_TEXT',
        Loc::getMessage($CONFIG_SUFFIX . "QUOTE_TEXT"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "QUOTE_TEXT", ""),
        ['limitedText'],
        65535,
        'required'
    ],
    [
//        $CONFIG_SUFFIX . 'QUOTE_AUTHOR_FIO',
//        Loc::getMessage($CONFIG_SUFFIX . "QUOTE_AUTHOR_FIO"),
//        Option::get($MODULE_ID, $CONFIG_SUFFIX . "QUOTE_AUTHOR_FIO", ""),
//        ['limitedText'],
    ],
    [
//        $CONFIG_SUFFIX . 'QUOTE_AUTHOR_PERSONAL_PHOTO',
//        Loc::getMessage($CONFIG_SUFFIX . "QUOTE_AUTHOR_PERSONAL_PHOTO"),
//        Option::get($MODULE_ID, $CONFIG_SUFFIX . "QUOTE_AUTHOR_PERSONAL_PHOTO", ""),
//        ['number'],
//       4,
//        'required'
    ],
    [
//        $CONFIG_SUFFIX . 'QUOTE_AUTHOR_POST',
//        Loc::getMessage($CONFIG_SUFFIX . "QUOTE_AUTHOR_POST"),
//        Option::get($MODULE_ID, $CONFIG_SUFFIX . "QUOTE_AUTHOR_POST", ""),
//        ['limitedText'],
    ],


];
$aTabs[] = [
    'DIV' => 'quote_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_QUOTE"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_QUOTE"),
    'OPTIONS' => $arFields['quote']
];

$arFields['production'] = [
    [
        $CONFIG_SUFFIX . 'PRODUCTION_TEXT',
        Loc::getMessage($CONFIG_SUFFIX . "PRODUCTION_TEXT"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_TEXT", ""),
        ['limitedText'],
        65535,
        'required'
    ],
    [
        $CONFIG_SUFFIX . 'PRODUCTION_TOTAL_WEIGHT',
        Loc::getMessage($CONFIG_SUFFIX . "PRODUCTION_TOTAL_WEIGHT"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_TOTAL_WEIGHT", ""),
        ['number'],
        5,
        'required',
        'float'
    ],
    [
        $CONFIG_SUFFIX . 'PRODUCTION_NOTE',
        Loc::getMessage($CONFIG_SUFFIX . "PRODUCTION_NOTE"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_NOTE", ""),
        ['limitedText'],
        65535,
        'required'
    ],
    [
        $CONFIG_SUFFIX . 'PRODUCTION_BOTTLED_OIL',
        Loc::getMessage($CONFIG_SUFFIX . "PRODUCTION_BOTTLED_OIL"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_BOTTLED_OIL", ""),
        ['number'],
        4,
        'required'
    ],
    [
        $CONFIG_SUFFIX . 'PRODUCTION_BULK_OIL',
        Loc::getMessage($CONFIG_SUFFIX . "PRODUCTION_BULK_OIL"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_BULK_OIL", ""),
        ['number'],
        4,
        'required'
    ],

];
$aTabs[] = [
    'DIV' => 'production_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_PRODUCTION_INDICATORS"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_PRODUCTION_INDICATORS"),
    'OPTIONS' => $arFields['production']
];

//Option::set($MODULE_ID, $CONFIG_SUFFIX . "IMAGE_UPLOADS", json_encode([]));
//Option::set($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_INFO", json_encode([]));
$arFields['shipped_products'] = [
    [
        $CONFIG_SUFFIX . 'SHIPPED_PRODUCTS_TOTAL_WEIGHT',
        Loc::getMessage($CONFIG_SUFFIX . "SHIPPED_PRODUCTS_TOTAL_WEIGHT"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "SHIPPED_PRODUCTS_TOTAL_WEIGHT", ""),
        ['number'],
        4,
        'required'
    ],
    [
        $CONFIG_SUFFIX . 'SHIPPED_PRODUCTS_YEAR',
        Loc::getMessage($CONFIG_SUFFIX . "SHIPPED_PRODUCTS_YEAR"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "SHIPPED_PRODUCTS_YEAR", ""),
        ['number'],
        4,
        'required'
    ],
    [
        $CONFIG_SUFFIX . 'SHIPPED_PRODUCTS_IMAGE_NOTE',
        Loc::getMessage($CONFIG_SUFFIX . "SHIPPED_PRODUCTS_IMAGE_NOTE"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "SHIPPED_PRODUCTS_IMAGE_NOTE", ""),
        ['limitedText'],
        65535,
        'required'
    ],
    [
        $CONFIG_SUFFIX . 'SHIPPED_PRODUCTS_MEAL_TOTAL_WEIGHT',
        Loc::getMessage($CONFIG_SUFFIX . "SHIPPED_PRODUCTS_MEAL_TOTAL_WEIGHT"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "SHIPPED_PRODUCTS_MEAL_TOTAL_WEIGHT", ""),
        ['number'],
        4,
        'required'
    ],
    [
        $CONFIG_SUFFIX . 'SHIPPED_PRODUCTS_MEAL_YEAR',
        Loc::getMessage($CONFIG_SUFFIX . "SHIPPED_PRODUCTS_MEAL_YEAR"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "SHIPPED_PRODUCTS_MEAL_YEAR", ""),
        ['number'],
        4,
        'required'
    ],
    [
        $CONFIG_SUFFIX . 'SHIPPED_PRODUCTS_MEAL_IMAGE_NOTE',
        Loc::getMessage($CONFIG_SUFFIX . "SHIPPED_PRODUCTS_MEAL_IMAGE_NOTE"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "SHIPPED_PRODUCTS_MEAL_IMAGE_NOTE", ""),
        ['limitedText'],
        65535,
        'required'
    ],
    [
        $CONFIG_SUFFIX . 'SHIPPED_PRODUCTS_QUOTE_TEXT',
        Loc::getMessage($CONFIG_SUFFIX . "SHIPPED_PRODUCTS_QUOTE_TEXT"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "SHIPPED_PRODUCTS_QUOTE_TEXT", ""),
        ['limitedText'],
        65535,
        'required'
    ],
];
$aTabs[] = [
    'DIV' => 'shipped_products_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_SHIPPED_PRODUCTS"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_SHIPPED_PRODUCTS"),
    'OPTIONS' => $arFields['shipped_products']
];


$arFields['import_map'] = [
    [
//        $CONFIG_SUFFIX . 'IMPORT_MAP',
//        Loc::getMessage($CONFIG_SUFFIX . "IMPORT_MAP"),
//        Option::get($MODULE_ID, $CONFIG_SUFFIX . "IMPORT_MAP", ""),
//        ['limitedText'],
    ],
    [
        $CONFIG_SUFFIX . 'IMPORT_MAP_NOTE',
        Loc::getMessage($CONFIG_SUFFIX . "IMPORT_MAP_NOTE"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "IMPORT_MAP_NOTE", ""),
        ['limitedText'],
        65535,
        'required'
    ],
    [
        $CONFIG_SUFFIX . 'IMPORT_QUOTE_TEXT',
        Loc::getMessage($CONFIG_SUFFIX . "IMPORT_QUOTE_TEXT"),
        Option::get($MODULE_ID, $CONFIG_SUFFIX . "IMPORT_QUOTE_TEXT", ""),
        ['limitedText'],
        65535,
        'required'
    ],

];
$aTabs[] = [
    'DIV' => 'import_map_settings',
    'TAB' => Loc::getMessage($CONFIG_SUFFIX . "TAB_IMPORT_MAP"),
    'TITLE' => Loc::getMessage($CONFIG_SUFFIX . "TAB_IMPORT_MAP"),
    'OPTIONS' => $arFields['import_map']
];

$quoteAuthor=[];
$preQuoteAuthor = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "QUOTE_AUTHOR", ""), true);
if (is_array($preQuoteAuthor)) {
    $quoteAuthor = $preQuoteAuthor;
}

$shippedQuoteAuthor=[];
$preQuoteAuthor = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "SHIPPED_PRODUCTS_QUOTE_AUTHOR", ""), true);
if (is_array($preQuoteAuthor)) {
    $shippedQuoteAuthor = $preQuoteAuthor;
}
$importQuoteAuthor=[];
$preQuoteAuthor = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "IMPORT_QUOTE_AUTHOR", ""), true);
if (is_array($preQuoteAuthor)) {
    $importQuoteAuthor = $preQuoteAuthor;
}

$selectedFiles = [];
$preSelectedFiles = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "NETWORK_IMGS", ""), true);
if (is_array($preSelectedFiles)) {
    $selectedFiles = $preSelectedFiles;
}
$selectedShippedFiles = [];
$preSelectedShippedFiles = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "SHIPPED_PRODUCTS_IMAGE", ""), true);
if (is_array($preSelectedShippedFiles)) {
    $selectedShippedFiles = $preSelectedShippedFiles;
}
$selectedShippedMealFiles = [];
$preSelectedShippedMealFiles = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "SHIPPED_PRODUCTS_MEAL_IMAGE", ""), true);
if (is_array($preSelectedShippedMealFiles)) {
    $selectedShippedMealFiles = $preSelectedShippedMealFiles;
}

$selectedProductsFiles = [];
$preSelectedProductsFiles = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTS_IMAGE", ""), true);
if (is_array($preSelectedProductsFiles)) {
    $selectedProductsFiles = $preSelectedProductsFiles;
}

$selectedProductsFiles2 = [];
$preSelectedProductsFiles2 = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTS_IMAGE2", ""), true);
if (is_array($preSelectedProductsFiles2)) {
    $selectedProductsFiles2 = $preSelectedProductsFiles2;
}

$selectedMapFiles = [];
$preSelectedMapFiles = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "IMPORT_MAP", ""), true);
if (is_array($preSelectedMapFiles)) {
    $selectedMapFiles = $preSelectedMapFiles;
}
$selectedTurnover = [];
$preSelectedTurnover = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "TURNOVER", ""), true);
if (is_array($preSelectedTurnover)) {
    $selectedTurnover = $preSelectedTurnover;
}
$selectedYear = [];
$preSelectedYear = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "YEAR", ""), true);
if (is_array($preSelectedYear)) {
    $selectedYear = $preSelectedYear;
}

$selectedProductsInfo = [];
$preSelectedProductsInfo = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_INFO", ""), true);
if (is_array($preSelectedProductsInfo)) {
    $selectedProductsInfo = $preSelectedProductsInfo;
}
$selectedProductsInfoImg = [];
$preSelectedProductsInfoImg = json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_INFO_IMG", ""), true);
if (is_array($preSelectedProductsInfoImg)) {
    $selectedProductsInfoImg = $preSelectedProductsInfoImg;
}
$testImg=json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "IMAGE_UPLOADS", ""),true);
$postFiles=json_decode(Option::get($MODULE_ID, $CONFIG_SUFFIX . "FILES", ""),true);
$productImage2Name = Option::get($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_IMAGE2_NAME", "");
$productImageName = Option::get($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_IMAGE_NAME", "");

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
        if(!empty($_FILES)){
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "FILES",json_encode($_FILES));
        }else{
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "FILES",json_encode([]));
        }

        $directionDeps = $request->getPost($CONFIG_SUFFIX . "QUOTE_AUTHOR");
        if (is_array($directionDeps)) {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "QUOTE_AUTHOR",json_encode($directionDeps));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "QUOTE_AUTHOR", json_encode([]));
        }


        $directionDeps = $request->getPost($CONFIG_SUFFIX . "SHIPPED_PRODUCTS_QUOTE_AUTHOR");
        if (is_array($directionDeps)) {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "SHIPPED_PRODUCTS_QUOTE_AUTHOR",json_encode($directionDeps));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "SHIPPED_PRODUCTS_QUOTE_AUTHOR", json_encode([]));
        }

        $directionDeps = $request->getPost($CONFIG_SUFFIX . "IMPORT_QUOTE_AUTHOR");
        if (is_array($directionDeps)) {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "IMPORT_QUOTE_AUTHOR",json_encode($directionDeps));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "IMPORT_QUOTE_AUTHOR", json_encode([]));
        }

        $directionDeps = $request->getPost($CONFIG_SUFFIX . "YEAR");
        if (is_array($directionDeps)) {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "YEAR",json_encode($directionDeps));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "YEAR", json_encode([]));
        }

        $directionDeps = $request->getPost($CONFIG_SUFFIX . "PRODUCTION_INFO");
        if (is_array($directionDeps)) {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_INFO",json_encode($directionDeps));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_INFO", json_encode([]));
        }

        $directionDeps = $request->getPost($CONFIG_SUFFIX . "PRODUCTION_INFO_IMG");
        if (is_array($directionDeps)) {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_INFO_IMG",json_encode($directionDeps));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_INFO_IMG", json_encode([]));
        }

        $directionDeps = $request->getPost($CONFIG_SUFFIX . "PRODUCTION_IMAGE2_NAME");
        if (!empty($directionDeps)) {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_IMAGE2_NAME",$directionDeps);
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_IMAGE2_NAME", '');
        }

        $directionDeps = $request->getPost($CONFIG_SUFFIX . "PRODUCTION_IMAGE_NAME");
        if (!empty($directionDeps)) {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_IMAGE_NAME",$directionDeps);
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTION_IMAGE_NAME", '');
        }

        $directionDeps = $request->getPost($CONFIG_SUFFIX . "TURNOVER");
        if (is_array($directionDeps)) {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "TURNOVER",json_encode($directionDeps));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "TURNOVER", json_encode([]));
        }

        $files=[];
        $directionDeps = $request->getPost($CONFIG_SUFFIX . "NETWORK_IMGS");
        if (is_array($directionDeps)) {
            foreach ($directionDeps as $key => $dep){
                if($key=='prevFileId'){
                    continue;
                }
                if($dep['name']){
                    $name=$dep['name'];
                    $files[$name][key($dep)]=current($dep);
                    continue;
                }
                $files[$name][key($dep)]=current($dep);
            }
            $files['prevFileId']=$directionDeps['prevFileId'];
            foreach ($files as $file){
                if(is_array($file)){
                    if($file['tmp_name']){
                        //$files['new_files'][]= $file['tmp_name'];
                        $arFile = \Bitrix\Main\UI\FileInput::prepareFile($file);
                        if (isset($arFile['tmp_name']) && !file_exists($arFile['tmp_name'])) {
                            $tmpFilesDir = \CTempFile::GetAbsoluteRoot();
                            $arFile['tmp_name'] = $tmpFilesDir . $arFile['tmp_name'];
                        }
                        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files')) {
                            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files');
                        }
                        $saveFileId = CFile::SaveFile($arFile, 'option_files');
                        $files['files_id'][] = $saveFileId;
                    }
                }
            }
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "NETWORK_IMGS", json_encode($files));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "NETWORK_IMGS", json_encode([]));
        }


        $array=array();
        $fileArray=array();
        if (!empty($_FILES['MCART_BLAGO_BLAGO_TODAY_IMAGE_UPLOADS'])) {
            $array = json_decode(json_encode($_FILES['MCART_BLAGO_BLAGO_TODAY_IMAGE_UPLOADS']),true);
            foreach ($array as $key=>$a){
                foreach ($a as $key2=> $file){
                    if($key=='error'){
                        if($file[0]!=0){
                            $file[0]='not found';
                        }
                    }
                    $fileArray[$key2][$key]=$file[0];
                }
            }
        }

        $files=[];
        $directionDeps = $request->getPost($CONFIG_SUFFIX . "IMAGE_UPLOADS");
        if(!empty($array)){
            $directionDeps['files']=$fileArray;
        }
        if (is_array($directionDeps)) {
//            foreach ($directionDeps as $key => $dep){
//                if($key=='prevFileId'){
//                    continue;
//                }
//            if(is_array($dep)){
//
//                if($dep['name']){
//
//                    $name=$dep['name'];
//
//                    $files[$name][key($dep)]=current($dep);
//                    continue;
//                }
//                $files[$name][key($dep)]=current($dep);
//            }

          //  }
            $files= $directionDeps['files'];
            $files['prevFileId']=$directionDeps['prevFileId'];
            foreach ($files as $file){

                if(is_array($file)){
                    if($file['tmp_name']){
                        //$files['new_files'][]= $file['tmp_name'];
                        $arFile = \Bitrix\Main\UI\FileInput::prepareFile($file);
                        if (isset($arFile['tmp_name']) && !file_exists($arFile['tmp_name'])) {
                            $tmpFilesDir = \CTempFile::GetAbsoluteRoot();
                            $arFile['tmp_name'] = $tmpFilesDir . $arFile['tmp_name'];
                        }
                        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files')) {
                            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files');
                        }
                        $saveFileId = CFile::SaveFile($arFile, 'option_files');
                        $files['files_id'][] = $saveFileId;
                    }
                }
            }

            Option::set($MODULE_ID, $CONFIG_SUFFIX . "IMAGE_UPLOADS", json_encode($files));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "IMAGE_UPLOADS", json_encode([]));
        }

        $files=[];
        $directionDeps = $request->getPost($CONFIG_SUFFIX . "NETWORK_IMGS");
        if (is_array($directionDeps)) {
            foreach ($directionDeps as $key => $dep){
                if($key=='prevFileId'){
                    continue;
                }
                if($dep['name']){
                    $name=$dep['name'];
                    $files[$name][key($dep)]=current($dep);
                    continue;
                }
                $files[$name][key($dep)]=current($dep);
            }
            $files['prevFileId']=$directionDeps['prevFileId'];
            foreach ($files as $file){
                if(is_array($file)){
                    if($file['tmp_name']){
                        //$files['new_files'][]= $file['tmp_name'];
                        $arFile = \Bitrix\Main\UI\FileInput::prepareFile($file);
                        if (isset($arFile['tmp_name']) && !file_exists($arFile['tmp_name'])) {
                            $tmpFilesDir = \CTempFile::GetAbsoluteRoot();
                            $arFile['tmp_name'] = $tmpFilesDir . $arFile['tmp_name'];
                        }
                        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files')) {
                            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files');
                        }
                        $saveFileId = CFile::SaveFile($arFile, 'option_files');
                        $files['files_id'][] = $saveFileId;
                    }
                }
            }
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "NETWORK_IMGS", json_encode($files));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "NETWORK_IMGS", json_encode([]));
        }


        $files=[];
        $directionDeps = $request->getPost($CONFIG_SUFFIX . "IMPORT_MAP");
        if (is_array($directionDeps)) {
            foreach ($directionDeps as $key => $dep){
                if($key=='prevFileId'){
                    continue;
                }
                if($dep['name']){
                    $name=$dep['name'];
                    $files[$name][key($dep)]=current($dep);
                    continue;
                }
                $files[$name][key($dep)]=current($dep);
            }
            $files['prevFileId']=$directionDeps['prevFileId'];
            foreach ($files as $file){
                if(is_array($file)){
                    if($file['tmp_name']){
                        //$files['new_files'][]= $file['tmp_name'];
                        $arFile = \Bitrix\Main\UI\FileInput::prepareFile($file);
                        if (isset($arFile['tmp_name']) && !file_exists($arFile['tmp_name'])) {
                            $tmpFilesDir = \CTempFile::GetAbsoluteRoot();
                            $arFile['tmp_name'] = $tmpFilesDir . $arFile['tmp_name'];
                        }
                        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files')) {
                            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files');
                        }
                        $saveFileId = CFile::SaveFile($arFile, 'option_files');
                        $files['files_id'][] = $saveFileId;
                    }
                }
            }
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "IMPORT_MAP", json_encode($files));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "IMPORT_MAP", json_encode([]));
        }

        $files=[];
        $directionDeps = $request->getPost($CONFIG_SUFFIX . "SHIPPED_PRODUCTS_IMAGE");
        if (is_array($directionDeps)) {
            foreach ($directionDeps as $key => $dep){
                if($key=='prevFileId'){
                    continue;
                }
                if($dep['name']){
                    $name=$dep['name'];
                    $files[$name][key($dep)]=current($dep);
                    continue;
                }
                $files[$name][key($dep)]=current($dep);
            }
            $files['prevFileId']=$directionDeps['prevFileId'];
            foreach ($files as $file){
                if(is_array($file)){
                    if($file['tmp_name']){
                        //$files['new_files'][]= $file['tmp_name'];
                        $arFile = \Bitrix\Main\UI\FileInput::prepareFile($file);
                        if (isset($arFile['tmp_name']) && !file_exists($arFile['tmp_name'])) {
                            $tmpFilesDir = \CTempFile::GetAbsoluteRoot();
                            $arFile['tmp_name'] = $tmpFilesDir . $arFile['tmp_name'];
                        }
                        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files')) {
                            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files');
                        }
                        $saveFileId = CFile::SaveFile($arFile, 'option_files');
                        $files['files_id'][] = $saveFileId;
                    }
                }
            }
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "SHIPPED_PRODUCTS_IMAGE", json_encode($files));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "SHIPPED_PRODUCTS_IMAGE", json_encode([]));
        }

        $files=[];
        $directionDeps = $request->getPost($CONFIG_SUFFIX . "PRODUCTS_IMAGE");
        if (is_array($directionDeps)) {
            foreach ($directionDeps as $key => $dep){
                if($key=='prevFileId'){
                    continue;
                }
                if($dep['name']){
                    $name=$dep['name'];
                    $files[$name][key($dep)]=current($dep);
                    continue;
                }
                $files[$name][key($dep)]=current($dep);
            }
            $files['prevFileId']=$directionDeps['prevFileId'];
            foreach ($files as $file){
                if(is_array($file)){
                    if($file['tmp_name']){
                        //$files['new_files'][]= $file['tmp_name'];
                        $arFile = \Bitrix\Main\UI\FileInput::prepareFile($file);
                        if (isset($arFile['tmp_name']) && !file_exists($arFile['tmp_name'])) {
                            $tmpFilesDir = \CTempFile::GetAbsoluteRoot();
                            $arFile['tmp_name'] = $tmpFilesDir . $arFile['tmp_name'];
                        }
                        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files')) {
                            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files');
                        }
                        $saveFileId = CFile::SaveFile($arFile, 'option_files');
                        $files['files_id'][] = $saveFileId;
                    }
                }
            }
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTS_IMAGE", json_encode($files));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTS_IMAGE", json_encode([]));
        }

        $files=[];
        $directionDeps = $request->getPost($CONFIG_SUFFIX . "PRODUCTS_IMAGE2");
        if (is_array($directionDeps)) {
            foreach ($directionDeps as $key => $dep){
                if($key=='prevFileId'){
                    continue;
                }
                if($dep['name']){
                    $name=$dep['name'];
                    $files[$name][key($dep)]=current($dep);
                    continue;
                }
                $files[$name][key($dep)]=current($dep);
            }
            $files['prevFileId']=$directionDeps['prevFileId'];
            foreach ($files as $file){
                if(is_array($file)){
                    if($file['tmp_name']){
                        //$files['new_files'][]= $file['tmp_name'];
                        $arFile = \Bitrix\Main\UI\FileInput::prepareFile($file);
                        if (isset($arFile['tmp_name']) && !file_exists($arFile['tmp_name'])) {
                            $tmpFilesDir = \CTempFile::GetAbsoluteRoot();
                            $arFile['tmp_name'] = $tmpFilesDir . $arFile['tmp_name'];
                        }
                        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files')) {
                            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files');
                        }
                        $saveFileId = CFile::SaveFile($arFile, 'option_files');
                        $files['files_id'][] = $saveFileId;
                    }
                }
            }
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTS_IMAGE2", json_encode($files));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "PRODUCTS_IMAGE2", json_encode([]));
        }


        $files=[];
        $directionDeps = $request->getPost($CONFIG_SUFFIX . "SHIPPED_PRODUCTS_MEAL_IMAGE");
        if (is_array($directionDeps)) {
            foreach ($directionDeps as $key => $dep){
                if($key=='prevFileId'){
                    continue;
                }
                if($dep['name']){
                    $name=$dep['name'];
                    $files[$name][key($dep)]=current($dep);
                    continue;
                }
                $files[$name][key($dep)]=current($dep);
            }
            $files['prevFileId']=$directionDeps['prevFileId'];
            foreach ($files as $file){
                if(is_array($file)){
                    if($file['tmp_name']){
                        //$files['new_files'][]= $file['tmp_name'];
                        $arFile = \Bitrix\Main\UI\FileInput::prepareFile($file);
                        if (isset($arFile['tmp_name']) && !file_exists($arFile['tmp_name'])) {
                            $tmpFilesDir = \CTempFile::GetAbsoluteRoot();
                            $arFile['tmp_name'] = $tmpFilesDir . $arFile['tmp_name'];
                        }
                        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files')) {
                            mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload/option_files');
                        }
                        $saveFileId = CFile::SaveFile($arFile, 'option_files');
                        $files['files_id'][] = $saveFileId;
                    }
                }
            }
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "SHIPPED_PRODUCTS_MEAL_IMAGE", json_encode($files));
        } else {
            Option::set($MODULE_ID, $CONFIG_SUFFIX . "SHIPPED_PRODUCTS_MEAL_IMAGE", json_encode([]));
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
?>
    <div class="adm-info-message-wrap adm-info-message-red">
        <div class="adm-info-message">
            <div class="adm-info-message-title">Ошибка</div>
            <p class="error__mess"></p>
           <br>
            <div class="adm-info-message-icon"></div>
        </div>
    </div>
<?php
if ($message) {
    echo $message->Show();
}

$tabControl->begin();
?>
    <form
        action="<?= $APPLICATION->getCurPage() ?>?mpage=<?= $PAGE_ID ?>"
        method="post"
        name="<?= $MODULE_SUFFIX ?>_settings"
        enctype="multipart/form-data" id="<?= $MODULE_SUFFIX ?>"
    class="myForm"
    >
        <?= bitrix_sessid_post() ?>
        <?php
        foreach ($aTabs as $aTab) {
            if ($aTab['OPTIONS']) {
                $tabControl->beginNextTab();
                __AdmSettingsDrawList($MODULE_ID, $aTab['OPTIONS']);
            }
            if ($aTab['DIV']==='annual_turnover_settings'){

            if(!empty($selectedYear)):
                $res=array_combine($selectedYear, $selectedTurnover);
                ksort($res);
                foreach ($res as $key => $val) {
                    $result[$key]=$val;
                }
              $couter=0;
                foreach ($result as $key => $res):
                ?>
            <tr class="annual__turnover">
                <td>
                    Год<span class="required"><sup>*</sup></span>
                    <a name="opt_MCART_BLAGO_BLAGO_TODAY_YEAR"></a>
                </td>
                <td>
                    <input type="text" class="ss req" inputmode="numeric" size="" maxlength="4" value="<?=$key?>" name="MCART_BLAGO_BLAGO_TODAY_YEAR[]">
                </td>
                <td>
                    Оборот<span class="required"><sup>*</sup></span>
                    <a name="opt_MCART_BLAGO_BLAGO_TODAY_TURNOVER"></a>
                </td>
                <td>
                    <input type="text" class="ss float req" inputmode="numeric" size="" maxlength="5" value="<?=$res?>" name="MCART_BLAGO_BLAGO_TODAY_TURNOVER[]">
                </td>
                <?if($couter!=0):?>
                <td  width="50%" class=""> <a href="#" class="remove"></a></td>
            <?endif;?>

            </tr>

                <?
            $couter++;
                endforeach;
                else:
                ?>
            <tr class="annual__turnover">
                <td>
                    Год<span class="required"><sup>*</sup></span>
                    <a name="opt_MCART_BLAGO_BLAGO_TODAY_YEAR"></a>
                </td>
                <td>
                    <input type="text" class="ss req" inputmode="numeric" size="" maxlength="4" value="" name="MCART_BLAGO_BLAGO_TODAY_YEAR[]">
                </td>
                <td>
                    Оборот<span class="required"><sup>*</sup></span>
                    <a name="opt_MCART_BLAGO_BLAGO_TODAY_TURNOVER"></a>
                </td>
                <td>
                    <input type="text" class="ss float req" inputmode="numeric" size="" maxlength="5" value="" name="MCART_BLAGO_BLAGO_TODAY_TURNOVER[]">
                </td>
            </tr>

                <?
                endif;
                ?>
            <tr>
                <td  width="50%" class="adm-detail-content-cell-l">
                    Добавить Показатели
                </td>
                <td  width="50%" class=""> <a href="#" class="add"></a></td>
            </tr>
            <script>
                $('body').on('click', 'a.add', function(){
                   $(this).closest('tbody').append(
                       '<tr class="annual__turnover"> ' +
                       '<td>Год<span class="required"><sup>*</sup></span> <a name="opt_MCART_BLAGO_BLAGO_TODAY_YEAR"></a> </td> ' +
                       '<td> <input type="text" class="ss req" inputmode="numeric" size="" maxlength="4" value="" name="MCART_BLAGO_BLAGO_TODAY_YEAR[]"> </td> ' +
                       '<td>Оборот<span class="required"><sup>*</sup></span> <a name="opt_MCART_BLAGO_BLAGO_TODAY_TURNOVER"></a> </td> ' +
                       '<td> <input type="text" class="ss float req" inputmode="numeric" size="" maxlength="5" value="" name="MCART_BLAGO_BLAGO_TODAY_TURNOVER[]"> ' +
                       '</td> <td  width="50%" class=""> <a href="#" class="remove"></a></td> </tr> </tr>'+
                       '<tr > ' +
                       '<td  width="50%" class="adm-detail-content-cell-l">Добавить Показатели </td> ' +
                       '<td  width="50%" class=""> <a href="#" class="add"></a></td> </tr> '
                    );
                    $(this).closest('tr').remove();
                   //tabControl.SelectTab('quote_settings');
                });
                $('body').on('click', 'a.remove', function(){
                    if(this.dataset.fileId){
                        let fileId='#file'+this.dataset.fileId;
                        $(fileId).remove();
                    }
                    $(this).closest('tr').remove();
                });
            </script>
                <?
            }
            if ($aTab['DIV'] === 'quote_settings') {
                ?>
            <tr>
            <td  width="50%" class="adm-detail-content-cell-l">
                Автор цитаты
            </td>
            <td  width="50%" class="adm-detail-content-cell-l">
            <?

            $GLOBALS["APPLICATION"]->IncludeComponent('bitrix:intranet.user.selector', '', array(
               'INPUT_NAME' =>$CONFIG_SUFFIX."QUOTE_AUTHOR",
                "MULTIPLE" => "N",
               'INPUT_NAME_STRING' => "estimate_contact_h",
               'INPUT_NAME_SUSPICIOUS' => "estimate_contact_h",
               'TEXTAREA_MIN_HEIGHT' => 30,
               'TEXTAREA_MAX_HEIGHT' => 60,
               'INPUT_VALUE' => $quoteAuthor ? $quoteAuthor : "",
               'EXTERNAL' => 'A',
               'SOCNET_GROUP_ID' => ""
                )
            );
            ?>
            </td>
        </tr>
                <?
            }
            if($aTab['DIV'] === 'networks_for_sale_settings'){
                ?>
            <tr class="heading" id="tr_LOGO_LABEL">
                <td colspan="2"><?=  Loc::getMessage($CONFIG_SUFFIX . "NETWORK_LOGO")?></td>
            </tr>
            <td colspan="2" align="center">
                <?
                echo \Bitrix\Main\UI\FileInput::createInstance(array(
                    "name" => $CONFIG_SUFFIX . 'NETWORK_IMGS[]',
                    "description" => true,
                    "upload" => true,
                    "allowUpload" => "I",
                    "medialib" => true,
                    "fileDialog" => true,
                    "cloud" => true,
                    "delete" => true,
                    //"maxCount" => 10
                ))->show(
                    $selectedFiles['files_id'] ? $selectedFiles['files_id'] : ($selectedFiles['prevFileId'] ? $selectedFiles['prevFileId'] : 0)
                );
                ?>
                <?
                if (!empty($selectedFiles['files_id']) || !empty($selectedFiles['prevFileId'])): ?>
                    <?foreach ($selectedFiles['files_id'] as $selectedFile):?>
                    <input type="hidden" class="oldVideo"
                           name="<?= $CONFIG_SUFFIX ?>NETWORK_IMGS[prevFileId][]"
                           value="<?=$selectedFile?>" id="file<?=$selectedFile?>">
                    <?endforeach;?>
                    <?foreach ($selectedFiles['prevFileId'] as $selectedFile):?>
                    <input type="hidden" class="oldVideo"
                           name="<?= $CONFIG_SUFFIX ?>NETWORK_IMGS[prevFileId][]"
                           value="<?=$selectedFile?>" id="file<?=$selectedFile?>">
                    <?endforeach;?>
                <?
                endif;
                ?>
            </td>
            <script>
                $('.adm-btn-del').click(function (event) {
                    let fileId='#file'+event.target.closest('td').querySelector('.adm-fileinput-item-preview input').value;
                    $(fileId).remove();
                });
            </script>
                <?
            }
            if($aTab['DIV'] === 'import_map_settings'){
                ?>
            <tr>
                <td  width="50%" class="adm-detail-content-cell-l">
                    Автор цитаты
                </td>
                <td  width="50%" class="adm-detail-content-cell-l">
                    <?
                    $GLOBALS["APPLICATION"]->IncludeComponent('bitrix:intranet.user.selector', '', array(
                            'INPUT_NAME' =>$CONFIG_SUFFIX."IMPORT_QUOTE_AUTHOR",
                            "MULTIPLE" => "N",
                            'INPUT_NAME_STRING' => "import_contact_h",
                            'INPUT_NAME_SUSPICIOUS' => "import_contact_h",
                            'TEXTAREA_MIN_HEIGHT' => 30,
                            'TEXTAREA_MAX_HEIGHT' => 60,
                            'INPUT_VALUE' => $importQuoteAuthor ? $importQuoteAuthor : "",
                            'EXTERNAL' => 'A',
                            'SOCNET_GROUP_ID' => ""
                        )
                    );
                    ?>
                </td>
            </tr>
            <tr class="heading" id="tr_LOGO_LABEL">
                <td colspan="2"><?=  Loc::getMessage($CONFIG_SUFFIX . "IMPORT_MAP")?></td>
            </tr>
            <td colspan="2" align="center">
                <?
                echo \Bitrix\Main\UI\FileInput::createInstance(array(
                    "name" => $CONFIG_SUFFIX . 'IMPORT_MAP[]',
                    "description" => true,
                    "upload" => true,
                    "allowUpload" => "I",
                    "medialib" => true,
                    "fileDialog" => true,
                    "cloud" => true,
                    "delete" => true,
                    "maxCount" => 1
                ))->show(
                    $selectedMapFiles['files_id'] ? $selectedMapFiles['files_id'] : ($selectedMapFiles['prevFileId'] ? $selectedMapFiles['prevFileId'] : 0)
                );
                ?>
                <?
                if (!empty($selectedMapFiles['files_id']) || !empty($selectedMapFiles['prevFileId'])): ?>
                    <?foreach ($selectedMapFiles['files_id'] as $selectedMapFile):?>
                        <input type="hidden" class="oldVideo"
                               name="<?= $CONFIG_SUFFIX ?>IMPORT_MAP[prevFileId][]"
                               value="<?=$selectedMapFile?>" id="file<?=$selectedMapFile?>">
                    <?endforeach;?>
                    <?foreach ($selectedMapFiles['prevFileId'] as $selectedMapFile):?>
                        <input type="hidden" class="oldVideo"
                               name="<?= $CONFIG_SUFFIX ?>IMPORT_MAP[prevFileId][]"
                               value="<?=$selectedMapFile?>" id="file<?=$selectedMapFile?>">
                    <?endforeach;?>

                <?
                endif;
                ?>
            </td>
            <script>
                $('.adm-btn-del').click(function (event) {
                    let fileId='#file'+event.target.closest('td').querySelector('.adm-fileinput-item-preview input').value;
                    $(fileId).remove();
                });
            </script>
                <?
            }
                if($aTab['DIV'] === 'shipped_products_settings'){

                ?>
            <tr>
                <td  width="50%" class="adm-detail-content-cell-l">
                    Автор цитаты
                </td>
                <td  width="50%" class="adm-detail-content-cell-l">
                    <?
                    $GLOBALS["APPLICATION"]->IncludeComponent('bitrix:intranet.user.selector', '', array(
                            'INPUT_NAME' =>$CONFIG_SUFFIX."SHIPPED_PRODUCTS_QUOTE_AUTHOR",
                            "MULTIPLE" => "N",
                            'INPUT_NAME_STRING' => "shipped_contact_h",
                            'INPUT_NAME_SUSPICIOUS' => "shipped_contact_h",
                            'TEXTAREA_MIN_HEIGHT' => 30,
                            'TEXTAREA_MAX_HEIGHT' => 60,
                            'INPUT_VALUE' => $shippedQuoteAuthor ? $shippedQuoteAuthor : "",
                            'EXTERNAL' => 'A',
                            'SOCNET_GROUP_ID' => ""
                        )
                    );
                    ?>
                </td>
            </tr>
            <tr class="heading" id="tr_LOGO_LABEL">
                <td colspan="2"><?=  Loc::getMessage($CONFIG_SUFFIX . "SHIPPED_PRODUCTS_IMAGE")?></td>
            </tr>
            <td colspan="2" align="center">
                <?
                echo \Bitrix\Main\UI\FileInput::createInstance(array(
                    "name" => $CONFIG_SUFFIX . 'SHIPPED_PRODUCTS_IMAGE[]',
                    "description" => true,
                    "upload" => true,
                    "allowUpload" => "I",
                    "medialib" => true,
                    "fileDialog" => true,
                    "cloud" => true,
                    "delete" => true,
                    "maxCount" => 1
                ))->show(
                    $selectedShippedFiles['files_id'] ? $selectedShippedFiles['files_id'] : ($selectedShippedFiles['prevFileId'] ? $selectedShippedFiles['prevFileId'] : 0)
                );
                ?>
                <?
                if (!empty($selectedShippedFiles['files_id']) || !empty($selectedShippedFiles['prevFileId'])): ?>
                    <?foreach ($selectedShippedFiles['files_id'] as $selectedShippedFile):?>
                        <input type="hidden" class="oldVideo"
                               name="<?= $CONFIG_SUFFIX ?>SHIPPED_PRODUCTS_IMAGE[prevFileId][]"
                               value="<?=$selectedShippedFile?>" id="file<?=$selectedShippedFile?>">
                    <?endforeach;?>
                    <?foreach ($selectedShippedFiles['prevFileId'] as $selectedShippedFile):?>
                        <input type="hidden" class="oldVideo"
                               name="<?= $CONFIG_SUFFIX ?>SHIPPED_PRODUCTS_IMAGE[prevFileId][]"
                               value="<?=$selectedShippedFile?>" id="file<?=$selectedShippedFile?>">
                    <?endforeach;?>

                <?
                endif;
                ?>
            </td>
            <script>
                $('.adm-btn-del').click(function (event) {
                    let fileId='#file'+event.target.closest('td').querySelector('.adm-fileinput-item-preview input').value;
                    $(fileId).remove();
                });
            </script>

            <tr class="heading" id="tr_LOGO_LABEL">
                <td colspan="2"><?=  Loc::getMessage($CONFIG_SUFFIX . "SHIPPED_PRODUCTS_MEAL_IMAGE")?></td>
            </tr>
            <td colspan="2" align="center">
                <?
                echo \Bitrix\Main\UI\FileInput::createInstance(array(
                    "name" => $CONFIG_SUFFIX . 'SHIPPED_PRODUCTS_MEAL_IMAGE[]',
                    "description" => true,
                    "upload" => true,
                    "allowUpload" => "I",
                    "medialib" => true,
                    "fileDialog" => true,
                    "cloud" => true,
                    "delete" => true,
                    "maxCount" => 1
                ))->show(
                    $selectedShippedMealFiles['files_id'] ? $selectedShippedMealFiles['files_id'] : ($selectedShippedMealFiles['prevFileId'] ? $selectedShippedMealFiles['prevFileId'] : 0)
                );
                ?>
                <?
                if (!empty($selectedShippedMealFiles['files_id']) || !empty($selectedShippedMealFiles['prevFileId'])): ?>
                    <?foreach ($selectedShippedMealFiles['files_id'] as $selectedShippedMealFile):?>
                        <input type="hidden" class="oldVideo"
                               name="<?= $CONFIG_SUFFIX ?>SHIPPED_PRODUCTS_MEAL_IMAGE[prevFileId][]"
                               value="<?=$selectedShippedMealFile?>" id="file<?=$selectedShippedMealFile?>">
                    <?endforeach;?>
                    <?foreach ($selectedShippedMealFiles['prevFileId'] as $selectedShippedMealFile):?>
                        <input type="hidden" class="oldVideo"
                               name="<?= $CONFIG_SUFFIX ?>SHIPPED_PRODUCTS_MEAL_IMAGE[prevFileId][]"
                               value="<?=$selectedShippedMealFile?>" id="file<?=$selectedShippedMealFile?>">
                    <?endforeach;?>

                <?
                endif;
                ?>
            </td>
            <script>
                $('.adm-btn-del').click(function (event) {
                    let fileId='#file'+event.target.closest('td').querySelector('.adm-fileinput-item-preview input').value;
                    $(fileId).remove();
                });
            </script>

            <?
        }
        if($aTab['DIV'] === 'production_settings'){
            ?>
<!--            <tr class="heading" id="tr_LOGO_LABEL">-->
<!--                <td colspan="2">--><?//=  Loc::getMessage($CONFIG_SUFFIX . "PRODUCTS_IMAGE")?><!--</td>-->
<!--            </tr>-->
<!--            <td colspan="1" align="center">-->
<!--                --><?//
//                echo \Bitrix\Main\UI\FileInput::createInstance(array(
//                    "name" => $CONFIG_SUFFIX . 'PRODUCTS_IMAGE[]',
//                    "description" => true,
//                    "upload" => true,
//                    "allowUpload" => "F",
//                    "allowUploadExt"=>"jpeg, jpg, png, svg",
//                    "medialib" => true,
//                    "fileDialog" => true,
//                    "cloud" => true,
//                    "delete" => true,
//                    "maxCount" => 1
//                ))->show(
//                    $selectedProductsFiles['files_id'] ? $selectedProductsFiles['files_id'] : ($selectedProductsFiles['prevFileId'] ? $selectedProductsFiles['prevFileId'] : 0)
//                );
//                ?>
<!--                --><?//
//                if (!empty($selectedProductsFiles['files_id']) || !empty($selectedProductsFiles['prevFileId'])): ?>
<!--                    --><?//foreach ($selectedProductsFiles['files_id'] as $selectedProductsFile):?>
<!--                        <input type="hidden" class="oldVideo"-->
<!--                               name="--><?//= $CONFIG_SUFFIX ?><!--PRODUCTS_IMAGE[prevFileId][]"-->
<!--                               value="--><?//=$selectedProductsFile?><!--" id="file--><?//=$selectedProductsFile?><!--">-->
<!--                    --><?//endforeach;?>
<!--                    --><?//foreach ($selectedProductsFiles['prevFileId'] as $selectedProductsFile):?>
<!--                        <input type="hidden" class="oldVideo"-->
<!--                               name="--><?//= $CONFIG_SUFFIX ?><!--PRODUCTS_IMAGE[prevFileId][]"-->
<!--                               value="--><?//=$selectedProductsFile?><!--" id="file--><?//=$selectedProductsFile?><!--">-->
<!--                    --><?//endforeach;?>
<!---->
<!--                --><?//
//                endif;
//                ?>
<!--            </td>-->
<!--            <td colspan="2" align="center">Культура<span class="required" style="margin-right: 10px"><sup>*</sup></span><input type="text" class="req "  value="--><?//=$productImageName?><!--" name="MCART_BLAGO_BLAGO_TODAY_PRODUCTION_IMAGE_NAME">  </td>-->
<!---->
<!--            <tr class="heading" id="tr_LOGO_LABEL">-->
<!--                <td colspan="2">--><?//=  Loc::getMessage($CONFIG_SUFFIX . "PRODUCTS_IMAGE2")?><!--</td>-->
<!--            </tr>-->
<!--            <td  colspan="1" align="center">-->
<!--                --><?//
//                echo \Bitrix\Main\UI\FileInput::createInstance(array(
//                    "name" => $CONFIG_SUFFIX . 'PRODUCTS_IMAGE2[]',
//                    "description" => true,
//                    "upload" => true,
//                    "allowUpload" => "F",
//                    "allowUploadExt"=>"jpeg, jpg, png, svg",
//                    "medialib" => true,
//                    "fileDialog" => true,
//                    "cloud" => true,
//                    "delete" => true,
//                    "maxCount" => 1
//                ))->show(
//                    $selectedProductsFiles2['files_id'] ? $selectedProductsFiles2['files_id'] : ($selectedProductsFiles2['prevFileId'] ? $selectedProductsFiles2['prevFileId'] : 0)
//                );
//                ?>
<!--                --><?//
//                if (!empty($selectedProductsFiles2['files_id']) || !empty($selectedProductsFiles2['prevFileId'])): ?>
<!--                    --><?//foreach ($selectedProductsFiles2['files_id'] as $selectedProductsFile):?>
<!--                        <input type="hidden" class="oldVideo"-->
<!--                               name="--><?//= $CONFIG_SUFFIX ?><!--PRODUCTS_IMAGE2[prevFileId][]"-->
<!--                               value="--><?//=$selectedProductsFile?><!--" id="file--><?//=$selectedProductsFile?><!--">-->
<!--                    --><?//endforeach;?>
<!--                    --><?//foreach ($selectedProductsFiles2['prevFileId'] as $selectedProductsFile):?>
<!--                        <input type="hidden" class="oldVideo"-->
<!--                               name="--><?//= $CONFIG_SUFFIX ?><!--PRODUCTS_IMAGE2[prevFileId][]"-->
<!--                               value="--><?//=$selectedProductsFile?><!--" id="file--><?//=$selectedProductsFile?><!--">-->
<!--                    --><?//endforeach;?>
<!---->
<!--                --><?//
//                endif;
//                ?>
<!--            </td>-->
<!---->
<!--            <td colspan="2" align="center">Культура<span class="required" style="margin-right: 10px"><sup>*</sup></span><input type="text" class="req " value="--><?//=$productImage2Name?><!--" name="MCART_BLAGO_BLAGO_TODAY_PRODUCTION_IMAGE2_NAME">  </td>-->
<?//

        if(!empty($testImg)):

        $result=array();
        if(!empty($testImg['files_id'])){
            foreach ($testImg['files_id'] as $file){
                $testImg['prevFileId'][]=$file;
            }
        }
$testImg['prevFileId']=array_unique($testImg['prevFileId']);


        $res=array_combine($selectedProductsInfo, $testImg['prevFileId']);
       ksort($res);
//$res=$selectedProductsInfo;
        foreach ($res as $key => $val) {
            $result[$key]=$val;
        }
        $couter=0;

        foreach ($result as $key => $res):
        ?>
        <tr class="annual__turnover">
            <td>
                Культура <span class="required"><sup>*</sup></span>
                <a name="opt_MCART_BLAGO_BLAGO_TODAY_PRODUCTION_INFO"></a>
            </td>
            <td>
                <input type="text" class="req" value="<?=$key?>" name="MCART_BLAGO_BLAGO_TODAY_PRODUCTION_INFO[]">
            </td>

            <td>
                Изображение культуры <span class="required"><sup>*</sup></span>
                <a name="opt_MCART_BLAGO_BLAGO_TODAY_PRODUCTION_INFO_IMG"></a>
            </td>
            <td>
                <?  $src = CFile::GetPath($res);
                $path_info=CFile::GetById($res)->fetch();
              //  $path_info['ORIGINAL_NAME']
                ?>
                <span class="adm-input-file">
                    <span>Добавить файл</span>
                    <input type="file" class="image_uploads adm-designed-file req" data-file-id="<?=$res?>" name="MCART_BLAGO_BLAGO_TODAY_IMAGE_UPLOADS[<?=$key?>][]" accept=".jpg, .jpeg, .png, .svg"
                           data-input="image_uploads" style="opacity: 0;" value="<?=$res?>">
                </span>
                <div class="preview"  data-input="image_uploads">
                    <p> <img src="<?=$src?>" style="width: 200px;"></p>
                </div>
            </td>

            <?if($couter!=0):?>
                <td  width="50%" class=""> <a href="#" class="remove" data-file-id="<?=$res?>"></a></td>
            <?endif;?>
        </tr>
        <?
            $couter++;
                endforeach;
                else:
        ?>
        <tr class="annual__turnover">
            <td>
                Культура <span class="required"><sup>*</sup></span>
                <a name="opt_MCART_BLAGO_BLAGO_TODAY_PRODUCTION_INFO"></a>
            </td>
            <td>
                <input type="text" class="req" value="" name="MCART_BLAGO_BLAGO_TODAY_PRODUCTION_INFO[]">
            </td>
            <td>
                Изображение культуры<span class="required"><sup>*</sup></span>
                <a name="opt_MCART_BLAGO_BLAGO_TODAY_PRODUCTION_INFO_IMG"></a>
            </td>
            <td class="img_loader">
                <div class="adm-input-file">
                    <span>Добавить файл</span>
                    <input type="file" class="image_uploads adm-designed-file req" name="MCART_BLAGO_BLAGO_TODAY_IMAGE_UPLOADS[1][]"
                           accept=".jpg, .jpeg, .png, .svg" data-file-id="" data-input="image_uploads" style="">
                </div>
                <div class="preview" data-input="image_uploads"><p></p></div>
            </td>

        </tr>
        <?endif;?>
<!--            --><?//
            if (!empty($testImg['prevFileId'])): ?>
                <?foreach ($testImg['prevFileId'] as $testIm):?>
            <input type="hidden" class="oldVideo"
                   name="<?= $CONFIG_SUFFIX ?>IMAGE_UPLOADS[prevFileId][]"
                   value="<?=$testIm?>" id="file<?=$testIm?>">
            <?endforeach;?>
            <?
            endif;
            ?>

            <tr>
                <td  width="50%" class="adm-detail-content-cell-l">
                    Добавить Показатели
                </td>
                <td  width="50%" class="" > <a href="#" class="addCulture"></a></td>
            </tr>
        <script>
            $('body').on('click', 'a.addCulture', function(){
                let _this=this;
               let counter= document.querySelectorAll('.image_uploads').length;
               counter+=1;
                $(this).closest('tbody').append(
                    '<tr class="annual__turnover"> ' +
                    '<td>Культура<span class="required"><sup>*</sup></span> <a name="opt_MCART_BLAGO_BLAGO_TODAY_PRODUCTION_INFO"></a> </td> ' +
                    '<td> <input type="text" class="req" value="" name="MCART_BLAGO_BLAGO_TODAY_PRODUCTION_INFO[]"> </td> ' +
                    '<td>Изображение культуры<span class="required"><sup>*</sup></span> <a name="opt_MCART_BLAGO_BLAGO_TODAY_IMAGE_UPLOADS"></a> </td> ' +
                    '<td>  <span class="adm-input-file"> <span>Добавить файл</span> <input type="file" data-file-id="" class="image_uploads adm-designed-file req" name="MCART_BLAGO_BLAGO_TODAY_IMAGE_UPLOADS['+counter+'][]"accept=".jpg, .jpeg, .png, .svg" data-input="image_uploads" style="opacity: 0;"> </span> ' +
                    '<div class="preview" data-input="image_uploads"><p></p></div>'+
                    '</td> <td  width="50%" class=""> <a href="#" class="remove"></a></td> </tr> </tr>'+
                    '<tr > ' +
                    '<td  width="50%" class="adm-detail-content-cell-l">Добавить Показатели </td> ' +
                    '<td  width="50%" class=""> <a href="#" class="addCulture"></a></td> </tr> '
                );
               $(this).closest('tr').remove();
            });
        </script>

            <?
        }

        }
?>

        <?php
        $tabControl->buttons(); ?>

        <input type="submit" name="Update"
               value="<?= Loc::getMessage($CONFIG_SUFFIX . 'SAVE') ?>"
               class="adm-btn-save"/>
    </form>
    <script>
        // let imgs=document.querySelectorAll('.image_uploads');
        // imgs.forEach((img) => {
        //     img.style.opacity = 0;
        //     img.addEventListener('change', updateImageDisplay);
        // });
        $('.adm-btn-del').click(function (event) {

        });
        $('body').on( "change", ".image_uploads", function() {
            if(this.dataset.fileId){
                let fileId='#file'+this.dataset.fileId;
                $(fileId).remove();
            }

            let _this=this;
           // console.log(this.closest('td').querySelector('.preview'));
            let preview = this.closest('td').querySelector('.preview');
          //  let span = this.closest('div');
            while(preview.firstChild) {
                preview.removeChild(preview.firstChild);
            }

            const curFiles = this.files;
            console.log(curFiles);
            if (curFiles.length === 0) {
                const para = document.createElement('p');
                para.textContent = 'No files currently selected for upload';
                preview.appendChild(para);
            } else {
                const list = document.createElement('div');
                preview.appendChild(list);
                for (const file of curFiles) {
                    const listItem = document.createElement('div');
                    const para = document.createElement('p');
                    if (validFileType(file)) {
                        if(file.size>4194304){
                            para.textContent = `file size ${returnFileSize(file.size)} too large`;
                            listItem.appendChild(para);
                        }else{
                          //  span.textContent=`${file.name}`;
                            para.textContent = `File name ${file.name}, file size ${returnFileSize(file.size)}.`;
                            const image = document.createElement('img');
                            image.src = URL.createObjectURL(file);
                            image.style.width='200px';
                            listItem.appendChild(image);
                            listItem.appendChild(para);
                        }
                    } else {
                        para.textContent = `File name ${file.name}: Not a valid file type. Update your selection.`;
                        listItem.appendChild(para);
                    }
                    list.appendChild(listItem);
                }
            }
        })

        const fileTypes = [
            "image/apng",
            "image/bmp",
            "image/gif",
            "image/jpeg",
            "image/pjpeg",
            "image/png",
            "image/svg+xml",
            "image/tiff",
            "image/webp",
            "image/x-icon"
        ];

        function validFileType(file) {
            return fileTypes.includes(file.type);
        }

        function returnFileSize(number) {
            if (number < 1024) {
                return `${number} bytes`;
            } else if (number >= 1024 && number < 1048576) {
                return `${(number / 1024).toFixed(1)} KB`;
            } else if (number >= 1048576) {
                return `${(number / 1048576).toFixed(1)} MB`;
            }
        }
        $(".ss").keypress(function(event){
            event = event || window.event;
            if (event.charCode && event.charCode!=0 && event.charCode!=44 && (event.charCode < 48 || event.charCode > 57) )
                return false;
        });
        $(".float").keypress(function(event){
            event = event || window.event;
            if (event.charCode && event.charCode!=0 && event.charCode!=44 && (event.charCode < 48 || event.charCode > 57) )
                return false;

        });
        $(".float").on("input",function (){
            let rx =/^\d{1,3}(?:,\d{1,2})?$/;
            let rx2 = /^(?!0+[1-9])\d{1,3}(,\d{1})?$/;

            console.log(rx2.test($(this).val()));
            if (rx2.test($(this).val())==false){
                $(this).css('color','red');
            }else{
                $(this).css('color','black');
            }
        });
        BX.ready(function() {
            //$(".float").mask("9,?9");
        //    $("#estimate_contact_h").attr("required",true);
            $("body").on("input",".red_error",function (){
              $(this).removeClass('red_error');
            });
        });

        $("body").on("submit", ".myForm", function(evt) {

            if($("input[name='MCART_BLAGO_BLAGO_TODAY_IMPORT_MAP[prevFileId][]']").length==0 && $("input[name='MCART_BLAGO_BLAGO_TODAY_IMPORT_MAP[][name]']").length==0){
                evt.preventDefault();
                tabControl.SelectTab('import_map_settings');
                $(".adm-info-message-red").css("display","block");
                $(".error__mess").text(" Обязательное поле Карта не заполнено.");
                return false;
            }
            // if($("input[name='MCART_BLAGO_BLAGO_TODAY_PRODUCTS_IMAGE2[prevFileId][]']").length==0 && $("input[name='MCART_BLAGO_BLAGO_TODAY_PRODUCTS_IMAGE2[][name]']").length==0){
            //     evt.preventDefault();
            //     tabControl.SelectTab('production_settings');
            //     $(".adm-info-message-red").css("display","block");
            //     $(".error__mess").text(" Обязательное поле Иллюстрация к показателям продукции не заполнено.");
            //     return false;
            // }
            // if($("input[name='MCART_BLAGO_BLAGO_TODAY_PRODUCTS_IMAGE[prevFileId][]']").length==0 && $("input[name='MCART_BLAGO_BLAGO_TODAY_PRODUCTS_IMAGE[][name]']").length==0){
            //     evt.preventDefault();
            //     tabControl.SelectTab('production_settings');
            //     $(".adm-info-message-red").css("display","block");
            //     $(".error__mess").text(" Обязательное поле Иллюстрация к показателям продукции не заполнено.");
            //     return false;
            // }
            if($("input[name='MCART_BLAGO_BLAGO_TODAY_SHIPPED_PRODUCTS_IMAGE[prevFileId][]']").length==0 && $("input[name='MCART_BLAGO_BLAGO_TODAY_SHIPPED_PRODUCTS_IMAGE[][name]']").length==0){
                evt.preventDefault();
                tabControl.SelectTab('shipped_products_settings');
                $(".adm-info-message-red").css("display","block");
                $(".error__mess").text("Обязательное поле Иллюстрация для отгруженной продукции не заполнено.");
                return false;
            }
            if($("input[name='MCART_BLAGO_BLAGO_TODAY_SHIPPED_PRODUCTS_MEAL_IMAGE[prevFileId][]']").length==0 && $("input[name='MCART_BLAGO_BLAGO_TODAY_SHIPPED_PRODUCTS_MEAL_IMAGE[][name]']").length==0){
                evt.preventDefault();
                tabControl.SelectTab('shipped_products_settings');
                $(".adm-info-message-red").css("display","block");
                $(".error__mess").text("Обязательное поле Иллюстрация к Шроту не заполнено.");
                return false;
            }
            if ($("input[name='MCART_BLAGO_BLAGO_TODAY_NETWORK_IMGS[prevFileId][]']").length==0 && $("input[name='MCART_BLAGO_BLAGO_TODAY_NETWORK_IMGS[][name]']").length==0){
                evt.preventDefault();
                tabControl.SelectTab('networks_for_sale_settings');
                $(".adm-info-message-red").css("display","block");
                $(".error__mess").text(" Обязательное поле Логотипы сетей не заполнено.");
                return false;
            }
            if($("input[name='MCART_BLAGO_BLAGO_TODAY_QUOTE_AUTHOR[]']").length==0 || $("input[name='MCART_BLAGO_BLAGO_TODAY_QUOTE_AUTHOR[]']").val()=='' ){
                evt.preventDefault();
                tabControl.SelectTab('quote_settings');
                $(".adm-info-message-red").css("display","block");
                $(".error__mess").text(" Обязательное поле Автор цитаты не заполнено.");
                return false;
            }
            if($("input[name='MCART_BLAGO_BLAGO_TODAY_SHIPPED_PRODUCTS_QUOTE_AUTHOR[]']").length==0 || $("input[name='MCART_BLAGO_BLAGO_TODAY_SHIPPED_PRODUCTS_QUOTE_AUTHOR[]']").val()=='' ){
                evt.preventDefault();
                tabControl.SelectTab('shipped_products_settings');
                $(".adm-info-message-red").css("display","block");
                $(".error__mess").text(" Обязательное поле Автор цитаты не заполнено.");
                return false;
            }
            if($("input[name='MCART_BLAGO_BLAGO_TODAY_IMPORT_QUOTE_AUTHOR[]']").length==0 || $("input[name='MCART_BLAGO_BLAGO_TODAY_IMPORT_QUOTE_AUTHOR[]']").val()=='' ){
                evt.preventDefault();
                tabControl.SelectTab('import_map_settings');
                $(".adm-info-message-red").css("display","block");
                $(".error__mess").text(" Обязательное поле Автор цитаты не заполнено.");
                return false;
            }


            let inputs = new Array();
            let tabId='';
            let rx2 = /^(?!0+[1-9])\d{1,3}(,\d{1})?$/;

            $('.req').each(function () {
                inputs.push(this.value);

                if(this.classList.contains("image_uploads")){
                    console.log(this.dataset.fileId);
                    if(this.dataset.fileId==""){
                        if(this.value==""){
                            console.log(this);
                            evt.preventDefault();
                            tabId= this.closest('.adm-detail-content').id;
                            tabControl.SelectTab(tabId);
                            this.focus();
                            this.classList.add("red_error");
                            return false;
                        }
                    }
                } else if(this.value==""){
                    evt.preventDefault();
                    tabId= this.closest('.adm-detail-content').id;
                    tabControl.SelectTab(tabId);
                    this.focus();
                    this.classList.add("red_error");
                    return false;
                }else if(this.classList.contains("float")){
                    if(rx2.test(this.value)==false){
                        evt.preventDefault();
                        tabId= this.closest('.adm-detail-content').id;
                        tabControl.SelectTab(tabId);
                        this.focus();
                        showTooltip(this);
                        this.classList.add("red_error");
                        //$(this).get(0).setCustomValidity('Invalid');

                      //  this.setCustomValidity('Username should only contain lowercase letters. e.g. john');
                        return false;
                    }
                }

            });
        });
        function showTooltip(e){
            let tooltipElem;
            let target = e;
            tooltipElem = document.createElement('div');
            tooltipElem.className = 'tooltip';
            tooltipElem.innerHTML = 'До 3 трёх символов целого числа, с 1 знаком после запятой';
            document.body.append(tooltipElem);
            let coords = target.getBoundingClientRect();

            let left = coords.left + (target.offsetWidth - tooltipElem.offsetWidth) / 2;
            if (left < 0) left = 0; // не заезжать за левый край окна

            let top = coords.top - tooltipElem.offsetHeight - 5;
            if (top < 0) { // если подсказка не помещается сверху, то отображать её снизу
                top = coords.top + target.offsetHeight + 5;
            }

            tooltipElem.style.left = left + 'px';
            tooltipElem.style.top = top + 'px';
            setTimeout(function () {
                tooltipElem.remove();
            }, 2000)

        }
    </script>
<?php
$tabControl->end();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");