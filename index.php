<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Мебельная компания");
?><?
$arrFilter = array (
    "IBLOCK_ID" => 5,
    "ACTIVE" => "Y",
    "PROPERTY_PREFERRED_DEAL_VALUE" => "да",
);
?> <?$APPLICATION->IncludeComponent(
    "bitrix:news.list",
    "slider_top",
    Array(
        "ACTIVE_DATE_FORMAT" => "d.m.Y",
        "ADD_SECTIONS_CHAIN" => "Y",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "36000",
        "CACHE_TYPE" => "A",
        "CHECK_DATES" => "N",
        "DETAIL_URL" => "/ads/#CODE#",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "DISPLAY_DATE" => "Y",
        "DISPLAY_NAME" => "Y",
        "DISPLAY_PICTURE" => "Y",
        "DISPLAY_PREVIEW_TEXT" => "Y",
        "DISPLAY_TOP_PAGER" => "N",
        "FIELD_CODE" => array("NAME","PREVIEW_TEXT","PREVIEW_PICTURE",""),
        "FILTER_NAME" => "arrFilter",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "IBLOCK_ID" => "5",
        "IBLOCK_TYPE" => "ads",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
        "INCLUDE_SUBSECTIONS" => "Y",
        "MESSAGE_404" => "",
        "NEWS_COUNT" => "20",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => "",
        "PAGER_TITLE" => "Новости",
        "PARENT_SECTION" => "",
        "PARENT_SECTION_CODE" => "",
        "PREVIEW_TRUNCATE_LEN" => "",
        "PROPERTY_CODE" => array("PRICE","PREFERRED_DEAL","ADDRESS",""),
        "SET_BROWSER_TITLE" => "Y",
        "SET_LAST_MODIFIED" => "N",
        "SET_META_DESCRIPTION" => "Y",
        "SET_META_KEYWORDS" => "Y",
        "SET_STATUS_404" => "N",
        "SET_TITLE" => "Y",
        "SHOW_404" => "N",
        "SORT_BY1" => "",
        "SORT_BY2" => "",
        "SORT_ORDER1" => "",
        "SORT_ORDER2" => "",
        "STRICT_SECTION_CHECK" => "N"
    )
);?>
    <div class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-3 mb-lg-0">
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        Array(
                            "AREA_FILE_SHOW" => "file",
                            "EDIT_TEMPLATE" => "",
                            "PATH" => "/include/advantages_1.php"
                        )
                    );?>
                </div>
                <div class="col-md-6 col-lg-4 mb-3 mb-lg-0">
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        Array(
                            "AREA_FILE_SHOW" => "file",
                            "EDIT_TEMPLATE" => "",
                            "PATH" => "/include/advantages_1.php"
                        )
                    );?>
                </div>
                <div class="col-md-6 col-lg-4 mb-3 mb-lg-0">
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:main.include",
                        "",
                        Array(
                            "AREA_FILE_SHOW" => "file",
                            "EDIT_TEMPLATE" => "",
                            "PATH" => "/include/advantages_1.php"
                        )
                    );?>
                </div>
            </div>
        </div>
    </div>
<?$APPLICATION->IncludeComponent(
    "bitrix:news.line",
    "new_ads",
    Array(
        "ACTIVE_DATE_FORMAT" => "d.m.Y",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "36000",
        "CACHE_TYPE" => "A",
        "DETAIL_URL" => "",
        "FIELD_CODE" => array("NAME","PREVIEW_PICTURE","PROPERTY_PRICE","PROPERTY_SQUARE","PROPERTY_COUNT_BATHS","PROPERTY_ADDRESS","PROPERTY_COUNT_BEDS","PROPERTY_COUNT_GARAGES","PROPERTY_GARAGES",""),
        "FILTER_NAME" => "arrFilter2",
        "IBLOCKS" => array("5"),
        "IBLOCK_TYPE" => "ads",
        "NEWS_COUNT" => "9",
        "SORT_BY1" => "TIMESTAMP_X",
        "SORT_BY2" => "",
        "SORT_ORDER1" => "DESC",
        "SORT_ORDER2" => ""
    )
);?> <?$APPLICATION->IncludeComponent(
    "bitrix:news.line",
    "servise_list",
    Array(
        "ACTIVE_DATE_FORMAT" => "d.m.Y",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "360000",
        "CACHE_TYPE" => "A",
        "DETAIL_URL" => "",
        "FIELD_CODE" => array("","PROPERTY_ICON","PROPERTY_LINK",""),
        "IBLOCKS" => array("6"),
        "IBLOCK_TYPE" => "services",
        "NEWS_COUNT" => "6",
        "SORT_BY1" => "ACTIVE_FROM",
        "SORT_BY2" => "SORT",
        "SORT_ORDER1" => "DESC",
        "SORT_ORDER2" => "ASC"
    )
);?> <?$APPLICATION->IncludeComponent(
    "bitrix:news.line",
    "news_line",
    Array(
        "ACTIVE_DATE_FORMAT" => "f j, Y",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "360000",
        "CACHE_TYPE" => "A",
        "DETAIL_URL" => "",
        "FIELD_CODE" => array("NAME","PREVIEW_TEXT","PREVIEW_PICTURE","DATE_CREATE",""),
        "IBLOCKS" => array("1"),
        "IBLOCK_TYPE" => "news",
        "NEWS_COUNT" => "3",
        "SORT_BY1" => "TIMESTAMP_X",
        "SORT_BY2" => "SORT",
        "SORT_ORDER1" => "DESC",
        "SORT_ORDER2" => "ASC"
    )
);?> <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>