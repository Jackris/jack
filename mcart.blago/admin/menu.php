<?php

defined('B_PROLOG_INCLUDED') || die;

global $APPLICATION;

use Bitrix\Main\Loader;

IncludeModuleLangFile(__FILE__);

$APPLICATION->SetAdditionalCSS("/bitrix/panel/main/mcart_blago_menu.css");

if ($APPLICATION->GetGroupRight('mcart.blago') == 'D') {
    return false;
}

if (!Loader::includeModule('mcart.blago')) {
    return false;
}

$aMenu = [
    'parent_menu' => 'global_menu_settings',
    'sort' => 50,
    'text' => GetMessage("MCART_BLAGO_MENU_ITEM_MAIN"),
    "icon" => "mcart_blago_menu_icon",
    "page_icon" => "mcart_blago_menu_icon",
    'items_id' => 'mcart_blago_item_main',
    "items" => [
        [
            "url" => 'mcart.blago_router.php?mpage=other',
            "text" => GetMessage("MCART_BLAGO_MENU_ITEM_OTHER"),
            'items_id' => 'mcart_blago_item_other'
        ],
        [
            "url" => 'mcart.blago_router.php?mpage=blagoToday',
            "text" => GetMessage("MCART_BLAGO_MENU_ITEM_BLAGO_TODAY"),
            'items_id' => 'mcart_blago_item_blago_today'
        ],
        [
            "url" => 'mcart.blago_router.php?mpage=blagoKiosk',
            "text" => GetMessage("MCART_BLAGO_MENU_ITEM_BLAGO_KIOSK"),
            'items_id' => 'mcart_blago_item_blago_kiosk'
        ],
        [
            "url" => 'mcart.blago_router.php?mpage=blagoPurposes',
            "text" => GetMessage("MCART_BLAGO_MENU_ITEM_BLAGO_PURPOSES"),
            'items_id' => 'mcart_blago_item_blago_purposes'
        ],
        [
            "url" => 'mcart.blago_router.php?mpage=blagoFeedback',
            "text" => GetMessage("MCART_BLAGO_MENU_ITEM_BLAGO_FEEDBACK"),
            'items_id' => 'mcart_blago_item_blago_feedback'
        ],
        [
            "url" => 'mcart.blago_router.php?mpage=eStaff',
            "text" => GetMessage("MCART_BLAGO_MENU_ITEM_BLAGO_ESTAFF"),
            'items_id' => 'mcart_blago_item_blago_estaff'
        ],
        [
            "url" => 'mcart.blago_router.php?mpage=hrLink',
            "text" => GetMessage("MCART_BLAGO_MENU_ITEM_BLAGO_HRLINK"),
            'items_id' => 'mcart_blago_item_blago_hrlink'
        ],
        [
            "url" => 'mcart.blago_router.php?mpage=applications',
            "text" => GetMessage("MCART_BLAGO_MENU_ITEM_BLAGO_APPLICATIONS"),
            'items_id' => 'mcart_blago_item_blago_applications'
        ],
    ]
];

if ($APPLICATION->GetGroupRight('mcart.blago') == 'W') {
    $aMenu["items"][] = [
        "url" => 'mcart.blago_router.php?mpage=1c',
        "text" => GetMessage("MCART_BLAGO_MENU_ITEM_1C"),
        'items_id' => 'mcart_blago_item_1c'
    ];
}

return $aMenu;