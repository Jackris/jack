<?php

namespace Mcart\Blago;

use \Bitrix\Rest\RestException;
use \Bitrix\Main\Localization\Loc;

class HighloadBlock
{
    private static $entity = \Bitrix\Highloadblock\HighloadBlockTable::class;

    const arAllowedOperations = ['', '!', '<', '<=', '>', '>=', '><', '!><', '?', '=', '!=', '%', '!%', '@'];
    const regexDateIso8601 = '/^\d{4}-(0[1-9]|1[0-2])-([12]\d|0[1-9]|3[01])([T\s](([01]\d|2[0-3])\:[0-5]\d|24\:00)(\:[0-5]\d([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3])\:?([0-5]\d)?)?)?$/';

    private const ERROR_HLBLOCK_GET_EMPTY_PARAMS = "ERROR_HLBLOCK_GET_EMPTY_PARAMS";

    private const select = [
        'ID',
        'NAME',
        'TABLE_NAME'
    ];

    private const order = [
        'ID' => 'ASC'
    ];

    private static function getOrder(): array
    {
        return self::order;
    }

    private static function getSelect(): array
    {
        return self::select;
    }

    private static function getFilter($params)
    {
        $filter = [];
        if ($params["HLBLOCK_ID"]) {
            $filter["ID"] = $params["HLBLOCK_ID"];
        }
        if ($params["HLBLOCK_NAME"]) {
            $filter["NAME"] = $params["HLBLOCK_NAME"];
        }
        if ($params["TABLE_NAME"]) {
            $filter["TABLE_NAME"] = $params["TABLE_NAME"];
        }

        return self::prepareFilter(is_array($filter) ? $filter : []);
    }

    private static function prepareFilter($arFilter, $fields = [])
    {
        if (is_array($arFilter)) {
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
                } else {
                    if (!in_array($operation, static::arAllowedOperations)) {
                        unset($arFilter[$key]);
                    } else {
                        if (!is_array($value) && preg_match(static::regexDateIso8601, $value)) {
                            $arFilter[$key] = \CRestUtil::unConvertDateTime($value);
                        }
                    }
                }
            }
        }

        return $arFilter;
    }

    public static function get($params)
    {
        if (!$params['HLBLOCK_ID'] && !$params['HLBLOCK_NAME'] && !$params['TABLE_NAME']) {
            throw new RestException(
                'Один из параметров HLBLOCK_ID/HLBLOCK_NAME/TABLE_NAME обязателен для заполнения',
                self::ERROR_HLBLOCK_GET_EMPTY_PARAMS
            );
        }

        $object = self::$entity::getList([
            'select' => self::getSelect(),
            'order' => self::getOrder(),
            'filter' => self::getFilter($params),
            'limit' => 1
        ]);

        if ($hlblock = $object->fetch()) {
            return $hlblock;
        } else {
            throw new RestException("HL-блок не найден", self::ERROR_HLBLOCK_NOT_FOUND);
        }
    }
}