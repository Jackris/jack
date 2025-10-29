<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Crm\ContactTable;
use Bitrix\Crm\DealTable;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Contract\Controllerable;

class CrmDealCreate extends \CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        return [
            'searchContacts' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                ],
            ],
            'createDeal' => [
                'prefilters' => [
                    new ActionFilter\Authentication,
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST,
                    ]),
                    new ActionFilter\Csrf,
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

    public function createDealAction(array $fields): array
    {
        if (!Loader::includeModule('crm')) {
            return ['success' => false, 'errors' => ['Модуль CRM не установлен']];
        }

        $entity = new \CCrmDeal(false);
        $newDealId = $entity->Add(
            $fields,
            true,
            [
                'DISABLE_REQUIRED_USER_FIELD_CHECK' => true,
            ]
        );

        if (!$newDealId)
        {
            return ['success' => false, 'errors' => $entity->LAST_ERROR];
        }

        return ['success' => true, 'newId' => $newDealId];
    }

    public function executeComponent()
    {
        CJSCore::Init(array("popup"));
        $this->includeComponentTemplate();
    }
}