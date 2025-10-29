<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var CCrmDealDetailsComponent $component */

\Bitrix\Main\UI\Extension::load('custom.crmdeal');

?>
<div id="deal_create_form">
    <h2>Создание сделки</h2>
        <div>
            <label >Название сделки:</label>
            <input type="text" id="deal_name" name="TITLE" required>
        </div>

        <div>
            <label for="contact-search">Контакт (поиск по ФИО):</label>
            <input type="text" id="contact_search" name="contact_search" placeholder="Введите ФИО контакта">
            <button type="button" id="create_contact">Создать новый контакт</button>
        </div>
        <div>
            <label>Сумма:</label>
            <input type="number" id="deal_sum" name="OPPORTUNITY">
        </div>

        <div>
            <label>Описание:</label>
            <textarea id="deal_description" name="deal_description"></textarea>
        </div>

        <button id="create_deal">Сохранить</button>

    <div id="mess"></div>

</div>

<!--<div id="contact_create_form" style="display:none;">
        <h2>Создание контакта</h2>
        <div>
            <label >Имя:</label>
            <input type="text" name="NAME" required>
        </div>

        <div>
            <label >Фамилия:</label>
            <input type="text" name="LAST_NAME" required>
        </div>
        <div>
            <label>Отчество:</label>
            <input type="text" name="SECOND_NAME">
        </div>

        <button id="save_contact_info">Сохранить</button>

        <div id="mess"></div>
</div>-->
