<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Localization\Loc;

$arActivityDescription = [
    'NAME' => 'Запись комментария в сделку',
    'DESCRIPTION' => 'Запись комментария в сделку',
    'TYPE' => 'activity',
    'CLASS' => 'CustomSetComment',
    'JSCLASS' => 'BizProcActivity',
    'CATEGORY' => [
        'ID' => 'other',
    ],
    'RETURN' => [
        'errorMessage' => [
            'NAME' => 'Строка с ошибкой',
            'TYPE' => 'string',
        ]
    ]
];
