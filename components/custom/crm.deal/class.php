<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Crm\ContactTable;
use Bitrix\Crm\DealTable;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Contract\Controllerable;

class CCustomCrmDealComponent extends \CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        return [
            'searchContacts' => [
                '-prefilters' => [
                    new ActionFilter\Authentication(),
                ],
            ],
            'createDeal' => [
                '-prefilters' => [
                    new ActionFilter\Authentication(),
                ],
            ],
        ];
    }

    public function searchContactsAction($query = '')
    {
        if (!Loader::includeModule('crm')) {
            return [];
        }

        $contacts = [];
        if (!empty($query)) {
            $result = ContactTable::getList([
                'select' => ['ID', 'NAME', 'LAST_NAME', 'SECOND_NAME'],
                'filter' => [
                    'LOGIC' => 'OR',
                    ['%NAME' => $query],
                    ['%LAST_NAME' => $query],
                    ['%SECOND_NAME' => $query],
                ],
                'limit' => 10
            ]);

            while ($contact = $result->fetch()) {
                $fullName = trim(implode(' ', array_filter([
                    $contact['NAME'],
                    $contact['LAST_NAME'],
                    $contact['SECOND_NAME']
                ])));
                $contacts[] = [
                    'ID' => $contact['ID'],
                    'NAME' => $fullName
                ];
            }
        }

        return $contacts;
    }

    public function createDealAction($deal_name = '', $contact_id = 0, $new_contact = null, $deal_sum = 0, $deal_description = '')
    {
        if (!Loader::includeModule('crm')) {
            return ['success' => false, 'errors' => ['Модуль CRM не установлен']];
        }

        $result = [
            'success' => false,
            'errors' => []
        ];

        if (empty($deal_name)) {
            $result['errors'][] = 'Название сделки обязательно';
        }

        if (empty($result['errors'])) {
            $contactId = $contact_id;

            // Создаем контакт, если указан
            if ($new_contact && is_array($new_contact)) {
                $contactFields = [
                    'NAME' => $new_contact['first_name'] ?? '',
                    'LAST_NAME' => $new_contact['last_name'] ?? '',
                    'SECOND_NAME' => $new_contact['second_name'] ?? ''
                ];

                $contactResult = ContactTable::add($contactFields);
                if (!$contactResult->isSuccess()) {
                    $result['errors'] = array_merge($result['errors'], $contactResult->getErrorMessages());
                } else {
                    $contactId = $contactResult->getId();
                }
            }

            if ($contactId > 0 || $contact_id > 0) {
                // Создаем сделку
                $dealFields = [
                    'TITLE' => $deal_name,
                    'OPPORTUNITY' => $deal_sum,
                    'COMMENTS' => $deal_description,
                    'STAGE_ID' => 'NEW',
                    'CONTACT_ID' => $contactId
                ];

                $dealResult = DealTable::add($dealFields);
                if ($dealResult->isSuccess()) {
                    $result['success'] = true;
                    $result['message'] = 'Сделка успешно создана';
                } else {
                    $result['errors'] = array_merge($result['errors'], $dealResult->getErrorMessages());
                }
            } else {
                $result['errors'][] = 'Не удалось создать контакт';
            }
        }

        return $result;
    }

    public function executeComponent()
    {
        $this->includeComponentTemplate();
    }
}