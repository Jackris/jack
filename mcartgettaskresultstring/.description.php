<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Localization\Loc;

$arActivityDescription = [
    'NAME' => 'Получение строки из результатов задачи',
    'DESCRIPTION' => 'Получение строки из результатов задачи',
    'TYPE' => 'activity',
    'CLASS' => 'McartGetTaskResultString',
    'JSCLASS' => 'BizProcActivity',
    'CATEGORY' => [
        'ID' => 'other',
    ],
    'RETURN' => [
        'resultString' => [
            'NAME' => 'Строка с результатом',
            'TYPE' => 'string',
        ]
    ]
];
