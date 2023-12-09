<?php

namespace Mcart\Blago;

use \Bitrix\Rest\RestException;
use \Bitrix\Main\Localization\Loc;

IncludeModuleLangFile(__FILE__);

class HighloadBlockElement
{
    private static $entity = \Bitrix\Highloadblock\HighloadBlockTable::class;
    private static $entityField = \Bitrix\Main\UserFieldTable::class;

    const arAllowedOperations = ['', '!', '<', '<=', '>', '>=', '><', '!><', '?', '=', '!=', '%', '!%', '@'];
    const regexDateIso8601 = '/^\d{4}-(0[1-9]|1[0-2])-([12]\d|0[1-9]|3[01])([T\s](([01]\d|2[0-3])\:[0-5]\d|24\:00)(\:[0-5]\d([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3])\:?([0-5]\d)?)?)?$/';

    const ERROR_HLBLOCK_ELEMENT_NOT_FOUND = "ERROR_HLBLOCK_ELEMENT_NOT_FOUND";
    const ERROR_HLBLOCK_ELEMENT_FIELD_SEARCH_ELEMENT = "ERROR_HLBLOCK_ELEMENT_FIELD_SEARCH_ELEMENT";
    const ERROR_HLBLOCK_ELEMENT_GET_EMPTY_PARAMS = "ERROR_HLBLOCK_ELEMENT_GET_EMPTY_PARAMS";
    const ERROR_HLBLOCK_ELEMENT_FIELD_SEARCH = "ERROR_HLBLOCK_ELEMENT_FIELD_SEARCH";
    const ERROR_HLBLOCK_ELEMENT_FIELDS_EMPTY = "ERROR_HLBLOCK_ELEMENT_FIELDS_EMPTY";
    const ERROR_HLBLOCK_ELEMENT_UPDATE = "ERROR_HLBLOCK_ELEMENT_UPDATE";
    const ERROR_HLBLOCK_ELEMENT_EMPLOYEE_FIELD = "ERROR_HLBLOCK_ELEMENT_EMPLOYEE_FIELD";
    const ERROR_HLBLOCK_ELEMENT_ENUM_FIELD = "ERROR_HLBLOCK_ELEMENT_ENUM_FIELD";
    const ERROR_HLBLOCK_ELEMENT_ADD = "ERROR_HLBLOCK_ELEMENT_ADD";

    const order = [
        'ID' => 'ASC'
    ];
    const select = [
        '*'
    ];
    const LIST_LIMIT = 50;

    private static function getOrder()
    {
        return self::order;
    }

    private static function prepareSelect($params = [])
    {
        $select = is_array($params['SELECT']) ? $params['SELECT'] : [];

        if (count($select) > 0) {
            if (array_search('ID', $select) === false) {
                $select[] = 'ID';
            }
        } else {
            $select = self::select;
        }

        return $select;
    }

    private static function prepareFilterFields(string $hlblock, array $params = []): array
    {
        $ent_hlblock = 'HLBLOCK_' . $hlblock;

        $filter[] = [
            'ENTITY_ID' => $ent_hlblock
        ];

        if ($params['FILTER']) {
            unset($params['FILTER']['HLBLOCK_ID']);
            $filter = array_merge($filter, $params['FILTER']);
        }
        return self::prepareFilter($filter);
    }

    private static function prepareFilter(array $arFilter, array $fields = []): array
    {
        foreach ($arFilter as $key => $value) {
            if (is_numeric($key)) {
                $arFilter[$key] = self::prepareFilter($value, $fields);
                continue;
            }

            if (preg_match('/^([^a-zA-Z]*)(.*)/', $key, $matches)) {
                $operation = $matches[1];
                $fieldId = $matches[2];
            } else {
                $operation = "";
                $fieldId = $key;
            }

            if ($fieldId === 'LOGIC') {
                continue;
            }

            if (!in_array($operation, static::arAllowedOperations)) {
                unset($arFilter[$key]);
            } elseif (!is_array($value) && preg_match(static::regexDateIso8601, $value)) {
                $arFilter[$key] = \CRestUtil::unConvertDateTime($value);
            }
        }

        return $arFilter;
    }

    private static function addParamsNavData($params, $start)
    {
        if ($start >= 0) {
            $addedParams = [
                'limit' => static::LIST_LIMIT,
                'offset' => intval($start)
            ];
        } else {
            $addedParams = [
                'limit' => static::LIST_LIMIT,
            ];
        }

        return array_merge($params, $addedParams);
    }

    private static function addResultNavData($result, $dataManager, $getListParams)
    {
        $total = $dataManager::getCount($getListParams['filter'] ?: []);

        $result['total'] = $total;

        if ($getListParams['offset'] + $getListParams['limit'] < $total) {
            $result['next'] = $getListParams['offset'] + $getListParams['limit'];
        }

        return $result;
    }

    public static function getFields(string $hlblock, array $params = []): array
    {
        $result = [];

        $obParams = [
            'filter' => self::prepareFilterFields($hlblock, $params)
        ];

        $dbHLFields = self::$entityField::getList($obParams);

        while ($hlField = $dbHLFields->fetch()) {
            $result[$hlField['FIELD_NAME']] = $hlField;
            if ($hlField['USER_TYPE_ID'] === 'enumeration') {
                $enOb = \CUserFieldEnum::GetList([], ['USER_FIELD_ID' => $hlField['ID']]);
                while ($en = $enOb->Fetch()) {
                    $result[$hlField['FIELD_NAME']]['VALUE'][$en['ID']] = $en;
                }
            }
        }

        return $result;
    }

    private static function getEntityDataClass(string $hlblock)
    {
        $hl = self::$entity::getById($hlblock)->fetch();
        return self::$entity::compileEntity($hl)->getDataClass();
    }

    private static function prepareDataUF(array $data, array $fields = []): array
    {
        $LegalEntitiesId = \Mcart\Blago\HighloadBlock::get([
            'HLBLOCK_NAME' => 'LegalEntities'
        ])['ID'];
        $LegalEntitiesDataClass = self::getEntityDataClass($LegalEntitiesId);

        foreach ($data as $fieldId => $value) {
            if (empty($value)) {
                continue;
            }

            $type = $fields[$fieldId]['USER_TYPE_ID'];
            $multi = $fields[$fieldId]['MULTIPLE'] === 'Y';

            if (
                $type === 'date' || $type === 'datetime'
            ) {
                if ($multi) {
                    $newValue = [];
                    foreach ($value as $key => $val) {
                        if (($val instanceof \Bitrix\Main\Type\Date) || ($val instanceof \Bitrix\Main\Type\DateTime)) {
                            $newValue[$key] = $val->format('Y-m-d\TH:i:s');
                        } else {
                            try {
                                if (strlen($val) > 10) {
                                    $newValue[$key] = new \Bitrix\Main\Type\DateTime($val);
                                    $newValue[$key] = $newValue[$key]->format('Y-m-d\TH:i:s');
                                } else {
                                    $newValue[$key] = new \Bitrix\Main\Type\Date($val);
                                    $newValue[$key] = $newValue[$key]->format('Y-m-d\TH:i:s');
                                }
                            } catch (\Bitrix\Main\SystemException $e) {
                                $newValue[$key] = null;
                            }
                        }
                    }

                    $data[$fieldId] = $newValue;
                } elseif (($value instanceof \Bitrix\Main\Type\Date) || ($value instanceof \Bitrix\Main\Type\DateTime)) {
                    $data[$fieldId] = $value->format('Y-m-d\TH:i:s');
                } else {
                    try {
                        if (strlen($value) > 10) {
                            $data[$fieldId] = new \Bitrix\Main\Type\DateTime($value);
                            $data[$fieldId] = $data[$fieldId]->format('Y-m-d\TH:i:s');
                        } else {
                            $data[$fieldId] = new \Bitrix\Main\Type\Date($value);
                            $data[$fieldId] = $data[$fieldId]->format('Y-m-d\TH:i:s');
                        }
                    } catch (\Bitrix\Main\SystemException $e) {
                        $data[$fieldId] = null;
                    }
                }
            } elseif ($type === 'file') {
                if ($multi) {
                    $data[$fieldId] = [];
                    foreach ($value as $key => $val) {
                        $file = \CFile::GetByID($val)->fetch();
                        $fileArray = \CFile::MakeFileArray($val);

                        if ($file) {
                            $base64file = base64_encode(file_get_contents($fileArray['tmp_name']));

                            $data[$fieldId][$key] = $file;
                            $data[$fieldId][$key]['BASE64'] = $base64file;
                        }
                    }
                } else {
                    $file = \CFile::GetByID($value)->fetch();
                    $fileArray = \CFile::MakeFileArray($value);

                    if ($file && $fileArray) {
                        $base64file = base64_encode(file_get_contents($fileArray['tmp_name']));

                        $data[$fieldId] = $file;
                        $data[$fieldId]['BASE64'] = $base64file;
                    }
                }
            } elseif ($type === 'enumeration') {
                if ($multi) {
                    $data[$fieldId] = [];
                    foreach ($value as $key => $val) {
                        $realValue = $fields[$fieldId]['VALUE'][$val];

                        if ($realValue) {
                            $data[$fieldId][$key] = $realValue['XML_ID'];
                        }
                    }
                } else {
                    $realValue = $fields[$fieldId]['VALUE'][$value];

                    if ($realValue) {
                        $data[$fieldId] = $realValue['XML_ID'];
                    }
                }
            } elseif ($type === 'employee') {
                if ($multi) {
                    $data[$fieldId] = [];
                    foreach ($value as $key => $val) {
                        $user = \CUser::GetByID($val)->fetch();

                        if ($user) {
                            $data[$fieldId][$key] = $user['XML_ID'];
                        }
                    }
                } else {
                    $user = \CUser::GetByID($value)->fetch();

                    if ($user) {
                        $data[$fieldId] = $user['XML_ID'];
                    }
                }
            } elseif ($type === 'hlblock' && isset($fields[$fieldId]['SETTINGS']['HLBLOCK_ID']) && (int)$fields[$fieldId]['SETTINGS']['HLBLOCK_ID'] === (int)$LegalEntitiesId) {
                if ($multi) {
                    $data[$fieldId] = [];
                    foreach ($value as $key => $val) {
                        $obParams = [
                            'select' => array("ID", "UF_XML_ID"),
                            'filter' => array("ID" => $val)
                        ];
                        $rsElement = $LegalEntitiesDataClass::getList($obParams);
                        if ($arElement = $rsElement->fetch()) {
                            $data[$fieldId][$key] = $arElement['UF_XML_ID'];
                        }
                    }
                } else {
                    $obParams = [
                        'select' => array("ID", "UF_XML_ID"),
                        'filter' => array("ID" => $value)
                    ];
                    $rsElement = $LegalEntitiesDataClass::getList($obParams);
                    if ($arElement = $rsElement->fetch()) {
                        $data[$fieldId] = $arElement['UF_XML_ID'];
                    }
                }
            } elseif (($value instanceof \Bitrix\Main\Type\Date) || ($value instanceof \Bitrix\Main\Type\DateTime)) {
                $data[$fieldId] = $value->format('c');
            }
        }

        return $data;
    }

    public static function get(string $hlblock, array $params): array
    {
        $result = [];

        if ($params['FIELD_SEARCH'] || $params['ID']) {
            if ($params['FIELD_SEARCH'] && empty($params[$params['FIELD_SEARCH']])) {
                throw new RestException(
                    'Поля FIELD_SEARCH нету в переданных параметрах элемента',
                    self::ERROR_HLBLOCK_ELEMENT_FIELD_SEARCH_ELEMENT
                );
            }
        } else {
            throw new RestException(
                'Один из параметров FIELD_SEARCH/ID обязателен для заполнения',
                self::ERROR_HLBLOCK_ELEMENT_GET_EMPTY_PARAMS
            );
        }

        $fields = self::getFields($hlblock, []);

        if ($params['FIELD_SEARCH'] && $params['FIELD_SEARCH'] != 'ID' && !isset($fields[$params['FIELD_SEARCH']])) {
            throw new RestException(
                'Данного поля нет у указаного hl-блока',
                self::ERROR_HLBLOCK_ELEMENT_FIELD_SEARCH
            );
        }

        $entityDataClass = self::getEntityDataClass($hlblock);

        if ($params['FIELD_SEARCH']) {
            $obParams = [
                'select' => array("*"),
                'filter' => array($params['FIELD_SEARCH'] => $params[$params['FIELD_SEARCH']])
            ];

            $rsElement = $entityDataClass::getList($obParams);
            if ($arElement = $rsElement->fetch()) {
                $arElement = self::prepareDataUF($arElement, $fields);
                $result = $arElement;
            } else {
                throw new RestException(
                    'Элемент не найден',
                    self::ERROR_HLBLOCK_ELEMENT_NOT_FOUND
                );
            }
        } elseif ($params['ID']) {
            $elementID = $params['ID'];

            $rsElement = $entityDataClass::getById($elementID);
            if ($arElement = $rsElement->fetch()) {
                $arElement = self::prepareDataUF($arElement, $fields);
                $result = $arElement;
            } else {
                throw new RestException(
                    'Элемент не найден',
                    self::ERROR_HLBLOCK_ELEMENT_NOT_FOUND
                );
            }
        }

        return $result;
    }

    public static function getList(string $hlblock, array $params, $start): array
    {
        $fields = self::getFields($hlblock, []);

        if (empty($params['FILTER'])) {
            $params['FILTER'] = array();
        }

        $entityDataClass = self::getEntityDataClass($hlblock);

        $obParams = self::addParamsNavData([
            'select' => self::prepareSelect($params),
            'filter' => self::prepareFilter($params['FILTER'], $fields),
            'order' => self::getOrder()
        ], $start);

        $rsElement = $entityDataClass::getList($obParams);

        $elements = [];
        while ($arElement = $rsElement->fetch()) {
            $arElement = self::prepareDataUF($arElement, $fields);
            $elements[$arElement['ID']] = $arElement;
        }
        return self::addResultNavData($elements, $entityDataClass, $obParams);
    }

    private static function getUserIdByXml(string $xml): int
    {
        if (!empty(trim($xml))) {
            $dbRes = \CUser::GetList(
                $by = "ID",
                $order = "ASC",
                ["XML_ID" => trim($xml)],
                ['SELECT' => 'ID']
            );
            if ($ob = $dbRes->Fetch()) {
                return $ob['ID'];
            }
        }

        return 0;
    }

    private static function prepareUFFieldHL(array $data, array $fields = []): array
    {
        $result = [];

        foreach ($fields as $fieldId => $field) {
            if (!array_key_exists($fieldId, $data)) {
                continue;
            }

            $value = $data[$fieldId];

            if (empty($value)) {
                $result[$fieldId] = false;
            } elseif (
                $field['USER_TYPE_ID'] === 'date' || $field['USER_TYPE_ID'] === 'datetime'
            ) {
                if ($field["MULTIPLE"] === 'Y') {
                    $result[$fieldId] = [];
                    foreach ($value as $key => $val) {
                        if (preg_match(static::regexDateIso8601, $val)) {
                            $result[$fieldId][$key] = \CRestUtil::unConvertDateTime($val);
                        }
                    }
                } elseif (preg_match(static::regexDateIso8601, $value)) {
                    $result[$fieldId] = \CRestUtil::unConvertDateTime($value);
                } else {
                    $result[$fieldId] = "";
                }
            } elseif (
                $field['USER_TYPE_ID'] === 'file'
            ) {
                if ($field["MULTIPLE"] === 'Y') {
                    $result[$fieldId] = [];
                    foreach ($value as $key => $val) {
                        if (isset($val['del'])) {
                            $result[$fieldId][$key] = $val;
                        } elseif (!is_array($val) && (stripos($val, "https://") === 0 || stripos(
                                    $val,
                                    "http://"
                                ) === 0)) {
                            $result[$fieldId][$key] = \CFile::MakeFileArray($val);
                        } else {
                            $result[$fieldId][$key] = \CRestUtil::saveFile($val);
                        }
                    }
                } elseif (isset($value['del'])) {
                    $result[$fieldId] = $value;
                } elseif (!is_array($value) && (stripos($value, "https://") === 0 || stripos(
                            $value,
                            "http://"
                        ) === 0)) {
                    $result[$fieldId] = \CFile::MakeFileArray($value);
                } else {
                    $result[$fieldId] = \CRestUtil::saveFile($value);
                }
            } elseif ($field['USER_TYPE_ID'] === 'employee') {
                if ($field["MULTIPLE"] === 'Y') {
                    $result[$fieldId] = [];
                    foreach ($value as $key => $val) {
                        $userId = self::getUserIdByXml($val);

                        if ((int)$userId <= 0) {
                            throw new RestException(
                                'Не найден пользовать с GUID ' . $val . ' в поле ' . $fieldId,
                                self::ERROR_HLBLOCK_ELEMENT_EMPLOYEE_FIELD
                            );
                        }

                        $result[$fieldId][] = $userId;
                    }
                } else {
                    $userId = self::getUserIdByXml($value);

                    if ((int)$userId <= 0) {
                        throw new RestException(
                            'Не найден пользовать с GUID ' . $value . ' в поле ' . $fieldId,
                            self::ERROR_HLBLOCK_ELEMENT_EMPLOYEE_FIELD
                        );
                    }

                    $result[$fieldId] = $userId;
                }
            } elseif ($field['USER_TYPE_ID'] === 'enumeration') {
                if ($field["MULTIPLE"] === 'Y') {
                    $result[$fieldId] = [];

                    foreach ($value as $key => $val) {
                        $enumId = false;
                        foreach ($field['VALUE'] as $enumValue) {
                            if ($enumValue['XML_ID'] === $val) {
                                $enumId = $enumValue['ID'];
                                break;
                            }
                        }

                        if ((int)$enumId <= 0) {
                            throw new RestException(
                                'Не найдено значение с XML_ID ' . $val . ' в поле ' . $fieldId,
                                self::ERROR_HLBLOCK_ELEMENT_ENUM_FIELD
                            );
                        }

                        $result[$fieldId][] = $enumId;
                    }
                } else {
                    $enumId = false;
                    foreach ($field['VALUE'] as $enumValue) {
                        if ($enumValue['XML_ID'] === $value) {
                            $enumId = $enumValue['ID'];
                            break;
                        }
                    }

                    if ((int)$enumId <= 0) {
                        throw new RestException(
                            'Не найдено значение с XML_ID ' . $value . ' в поле ' . $fieldId,
                            self::ERROR_HLBLOCK_ELEMENT_ENUM_FIELD
                        );
                    }

                    $result[$fieldId] = $enumId;
                }
            } else {
                $result[$fieldId] = $value;
            }
        }

        return $result;
    }

    public static function update($hlblock, $params)
    {
        if (empty($params['ID'])) {
            $elementID = self::get($hlblock, $params)['ID'];
        } else {
            $elementID = $params['ID'];
        }

        if (
            !isset($params['FIELDS']) ||
            !is_array($params['FIELDS']) ||
            (is_array($params['FIELDS']) && count($params['FIELDS']) <= 0)
        ) {
            throw new RestException(
                'В ключе FIELDS требуется хотя бы одно поле',
                self::ERROR_HLBLOCK_ELEMENT_FIELDS_EMPTY
            );
        }

        $fields = self::getFields($hlblock, []);

        $entityDataClass = self::getEntityDataClass($hlblock);

        $data = self::prepareUFFieldHL($params['FIELDS'], $fields);
        $result = $entityDataClass::update($elementID, $data);

        if ($result->isSuccess()) {
            $result = true;
        } else {
            throw new RestException($result->getErrorMessages(), self::ERROR_HLBLOCK_ELEMENT_UPDATE);
        }

        return $elementID;
    }

    public static function add($hlblock, $params)
    {
        if (
            !isset($params['FIELDS']) ||
            !is_array($params['FIELDS']) ||
            (is_array($params['FIELDS']) && count($params['FIELDS']) <= 0)
        ) {
            throw new RestException(
                'В ключе FIELDS требуется хотя бы одно поле',
                self::ERROR_HLBLOCK_ELEMENT_FIELDS_EMPTY
            );
        }

        $elementID = null;
        $fields = self::getFields($hlblock, []);

        $entityDataClass = self::getEntityDataClass($hlblock);

        $data = self::prepareUFFieldHL($params['FIELDS'], $fields);
        $result = $entityDataClass::add($data);

        if ($result->isSuccess()) {
            $elementID = $result->getId();
        } else {
            throw new RestException($result->getErrorMessages(), self::ERROR_HLBLOCK_ELEMENT_ADD);
        }

        return $elementID;
    }
}