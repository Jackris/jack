<?php

namespace Mycompany\Dealer\Controller;

use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Controller;

class DealerAPI extends Controller
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

    public static function getModelsAction(string $dealerName)
    {
        $arDealers = \Mycompany\Dealer\ORM\DealerTable::getList([
            'select' => [
                'NAME','CARS'
            ],
            'filter' => [
                'NAME' => $dealerName,
                '!CARS.ID' => null
            ]
        ]);
        while ($dealer = $arDealers->fetch()){
            print_r($dealer);
        }

    }
}