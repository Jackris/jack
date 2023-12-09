<?php

namespace Mcart\Blago\Controller;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;

class Help extends Controller
{
    private const MODULE_ID = "mcart.blago";

    private const DISABLE_AUTH_COMPONENTS = [
        'bitrix:pdf.viewer'
    ];

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function configureActions(): array
    {
        return [
            'setOption' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST
                    ]),
                    new ActionFilter\Authentication(),
                    new ActionFilter\Csrf(),
                ],
            ],
            'saveLeftMenuItemsSort' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST
                    ]),
                    new ActionFilter\Authentication(),
                    new ActionFilter\Csrf(),
                ],
            ],
            'getComponent' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST
                    ]),
                    new ActionFilter\Authentication(),
                    new ActionFilter\Csrf(),
                ],
            ],
            'getComponentDisableAuthCheck' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST
                    ]),
                    new ActionFilter\Csrf(),
                ],
            ],
            'sendVmiQuery' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST
                    ]),
                    new ActionFilter\Csrf(),
                ],
            ],
            'saveGroupMenu' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST
                    ]),
                    new ActionFilter\Csrf(),
                ],
            ],
            'getPositions' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_GET
                    ]),
                    new ActionFilter\Csrf(),
                ],
            ]
        ];
    }

    private function getPositionHlBlockId()
    {
        $ob = HighloadBlockTable::getList([
            'select' => ['ID'],
            'order' => ['ID' => 'ASC'],
            'filter' => [
                '=TABLE_NAME' => 'staff_schedule',
            ],
            'limit' => 1,
        ]);

        if ($row = $ob->fetch()) {
            return $row['ID'];
        }

        return 0;
    }

    public function getPositionsAction($page, $search = '', $ids = [])
    {
        try {
            if (
                !\Bitrix\Main\Loader::includeModule('highloadblock')
            ) {
                throw new \Bitrix\Main\SystemException('Ошибка, попробуйте позже!');
            }

            $position_hl_block_id = $this->getPositionHlBlockId();
            if ($position_hl_block_id <= 0) {
                throw new \Bitrix\Main\SystemException('Ошибка, попробуйте позже!');
            }

            $hlblock = HighloadBlockTable::getById($position_hl_block_id)->fetch();
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $positionEntityDataClass = $entity->getDataClass();

            $items = [];

            $dbQuery = $positionEntityDataClass::query()
                ->setSelect([
                    'ID',
                    'UF_NAME',
                ])
                ->setOrder([
                    'UF_NAME' => 'asc'
                ])
                ->countTotal(1);
            if (is_string($search) && trim($search) !== '') {
                $dbQuery->whereLike('UF_NAME', '%' . $search . '%');
            }
            if ($page > 1 && is_array($ids) && count($ids) > 0) {
                $dbQuery->whereNotIn('ID', $ids);
            }
            $dbQuery->setLimit(50);
            $dbQuery->setOffset(($page - 1) * 50);
            $queryCollection = $dbQuery->exec();
            $isMore = $queryCollection->getCount() > (($page - 1) * 50);

            while ($obQuery = $queryCollection->fetch()) {
                $items[] = array(
                    'id' => $obQuery['ID'],
                    'value' => $obQuery['UF_NAME'] . ' [' . $obQuery['ID'] . ']',
                    'text' => $obQuery['UF_NAME'] . ' [' . $obQuery['ID'] . ']',
                    'position' => ''
                );
            }

            return [
                "results" => array_values($items),
                "pagination" => [
                    "more" => $isMore
                ]
            ];
        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    public function sendVmiQueryAction($text)
    {
        try {
            return;
        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    public function getComponentDisableAuthCheckAction($component, $template, $params)
    {
        if (!in_array($component, self::DISABLE_AUTH_COMPONENTS)) {
            return;
        }

        try {
            ob_start();

            require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

            global $APPLICATION;

            $APPLICATION->ShowAjaxHead();

            $APPLICATION->IncludeComponent(
                $component,
                $template,
                $params,
                false
            );

            \CMain::FinalActions();

            return ob_get_clean();
        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    public function getComponentAction($component, $template, $params)
    {
        try {
            ob_start();

            require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

            global $APPLICATION;

            $APPLICATION->ShowAjaxHead();

            $APPLICATION->IncludeComponent(
                $component,
                $template,
                $params,
                false
            );

            \CMain::FinalActions();

            return ob_get_clean();
        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    public function setOptionAction($name, $value)
    {
        try {
            return Option::set(self::MODULE_ID, $name, $value);
        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    public function saveLeftMenuItemsSortAction($items, $firstItemLink, $siteID)
    {
        if (!$siteID) {
            $dbSite = \CSite::GetList($by = "sort", $order = "desc", array("DEFAULT" => "Y"));
            if ($arSite = $dbSite->Fetch()) {
                $siteID = $arSite["LID"];
            }
        }

        $optionName = "left_menu_sorted_items_" . $siteID;
        foreach (array("show", "hide") as $status) {
            if (isset($items[$status]) && is_array($_POST["items"][$status])) {
                $userOption[$status] = $_POST["items"][$status];
            } else {
                $userOption[$status] = array();
            }
        }

        \CUserOptions::SetOption("intranet", $optionName, $userOption);

        if (isset($firstItemLink)) {
            \CUserOptions::SetOption("intranet", "left_menu_first_page_" . $siteID, $firstItemLink);
        }

        return true;
    }

    public function saveGroupMenuAction($groupId, $items)
    {
        $row = \Mcart\Blago\ORM\GroupSettingsMenuTable::query()
            ->where('ID', '=', $groupId)
            ->setSelect(['ID'])
            ->exec()
            ->fetch();

        if ($row) {
            \Mcart\Blago\ORM\GroupSettingsMenuTable::update($groupId, [
                'ITEMS' => json_encode($items)
            ]);
        } else {
            return \Mcart\Blago\ORM\GroupSettingsMenuTable::add([
                'ID' => $groupId,
                'ITEMS' => json_encode($items)
            ]);
        }
    }
}