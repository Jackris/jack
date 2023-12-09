<?php

namespace Mycompany\Dealer\Controller;

use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;

class BP extends Controller
{
    private const MODULE_ID = "mycompany.dealer";

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function configureActions(): array
    {
        return [
            'getModelsAction' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST,
                    ]),
                    new ActionFilter\Authentication(),
                    new ActionFilter\Csrf(),
                ],
            ],
        ];
    }

    public function getModelsAction($taskId)
    {
    /*use Bitrix\Main\Loader;
        Loader::includeModule('mycompany.dealer');*/
        $test = \Mycompany\Dealer\ORM\DealerTable::getList(array(
            'select' => array('*'),
        ));
        print_r($test->fetch());
    }
}