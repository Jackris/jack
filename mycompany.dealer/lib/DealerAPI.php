<?php

namespace Mycompany\Dealer;

use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Controller;

class DealerAPI extends Controller
{
    private const MODULE_ID = "mycompany.dealer";

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    /**
     * @return array[]
     */
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

    /**
     * Получение моделей, которые продает дилер
     * @param string $dealerName
     * @return array
     */
    public static function getModelsAction(string $dealerName)
    {
        $result = [];
        $arDealers = \Mycompany\Dealer\DealerTable::getList([
            'select' => [
                'NAME',
                'CARS'
            ],
            'filter' => [
                'NAME' => $dealerName,
                '!CARS.ID' => null
            ]
        ]);
        while ($dealer = $arDealers->fetch()) {
            $result[] = $dealer['MYCOMPANY_DEALER_ORM_DEALER_CARS_MODEL'];
        }
        print_r($result);
        return $result;
    }
}