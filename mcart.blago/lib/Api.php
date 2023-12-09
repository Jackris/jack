<?php

namespace Mcart\Blago;

use \Bitrix\Main\Loader;
use \Bitrix\Rest\RestException;
use \Bitrix\Main\Localization\Loc;
use Bitrix\Highloadblock\HighloadBlockTable;

IncludeModuleLangFile(__FILE__);

class Api extends \IRestService
{
    private const MODULE_ID = "mcart.blago";
    private const ERROR_INTERNAL = "ERROR_INTERNAL";

    private static $langs = null;
    private static $defaultLang = null;

    private static $currentLang = null;

    private static function setCurrentLang(string $lang): void
    {
        self::$currentLang = $lang && in_array($lang, (array)self::$langs, true) ? $lang : self::$defaultLang;
        \Bitrix\Main\Localization\Loc::setCurrentLang(self::$currentLang);
    }

    private static function getCurrentLang(): string
    {
        return self::$currentLang;
    }

    public static function getLangs(): array
    {
        $result = [];

        $rsLang = \CLanguage::GetList($by = "lid", $order = "desc", ["ACTIVE" => "Y"]);
        while ($arLang = $rsLang->Fetch()) {
            $result[] = $arLang['LID'];
        }

        return $result;
    }

    public static function OnRestServiceBuildDescription(): array
    {
        if (!Loader::includeModule('highloadblock')) {
            return [];
        }

        if (self::$langs === null) {
            self::$langs = self::getLangs();
        }

        if (self::$defaultLang === null) {
            self::$defaultLang = \Bitrix\Main\Config\Option::get("main", "admin_lid", "en");
        }

        return [
            self::MODULE_ID => [
                'mcart.1c.highloadblock.element.add' => [
                    'callback' => [__CLASS__, 'highloadblockElementAdd'],
                    'options' => [],
                ],
                'mcart.1c.highloadblock.element.update' => [
                    'callback' => [__CLASS__, 'highloadblockElementUpdate'],
                    'options' => [],
                ],
                'mcart.1c.highloadblock.element.get' => [
                    'callback' => [__CLASS__, 'highloadblockElementGet'],
                    'options' => [],
                ],
                'mcart.1c.highloadblock.element.list' => [
                    'callback' => [__CLASS__, 'highloadblockElementList'],
                    'options' => [],
                ],
                'mcart.1c.highloadblock.fields' => array(
                    'callback' => array(__CLASS__, 'highloadblockGetFields'),
                    'options' => array(),
                ),
                'mcart.1c.buh.head.get' => array(
                    'callback' => array(__CLASS__, 'buhHeadGet'),
                    'options' => array(),
                ),
                'mcart.1c.buh.lead.get' => array(
                    'callback' => array(__CLASS__, 'buhLeadGet'),
                    'options' => array(),
                ),
                'mcart.1c.hr.employees.get' => array(
                    'callback' => array(__CLASS__, 'hrEmployeesGet'),
                    'options' => array(),
                ),
                'mcart.1c.file.list' => array(
                    'callback' => array(__CLASS__, 'fileLists'),
                    'options' => array(),
                ),
            ]
        ];
    }

    private static function getEmployeesHlBlockId()
    {
        $ob = HighloadBlockTable::getList([
            'select' => ['ID'],
            'order' => ['ID' => 'ASC'],
            'filter' => [
                '=TABLE_NAME' => 'employees',
            ],
            'limit' => 1,
        ]);

        if ($row = $ob->fetch()) {
            return $row['ID'];
        }

        return 0;
    }

    private static function getLegalHlBlockId()
    {
        $ob = HighloadBlockTable::getList([
            'select' => ['ID'],
            'order' => ['ID' => 'ASC'],
            'filter' => [
                '=TABLE_NAME' => 'legal_entities',
            ],
            'limit' => 1,
        ]);

        if ($row = $ob->fetch()) {
            return $row['ID'];
        }

        return 0;
    }

    public static function hrEmployeesGet($params, $start, $server)
    {
        try {
            if (
                !\Bitrix\Main\Loader::includeModule('highloadblock')
            ) {
                throw new \Bitrix\Main\SystemException('Ошибка, попробуйте позже!');
            }

            $result = [];

            $legal_hl_block_id = self::getLegalHlBlockId();
            if ($legal_hl_block_id <= 0) {
                throw new \Bitrix\Main\SystemException('Ошибка, попробуйте позже!');
            }

            $hlblock = HighloadBlockTable::getById($legal_hl_block_id)->fetch();
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $legalEntityDataClass = $entity->getDataClass();

            $dbQuery = $legalEntityDataClass::query()
                ->setSelect([
                    '*'
                ]);
            $queryCollection = $dbQuery->exec();

            $allPosition = [];

            while ($row = $queryCollection->fetch()) {
                $result[$row['ID']] = [
                    'position_head' => false,
                    'position_hrs' => false,
                    'legal' => $row['UF_XML_ID'],
                    'head' => false,
                    'hrs' => []
                ];

                $preHrRespPosition = json_decode(
                    \Bitrix\Main\Config\Option::get(
                        self::MODULE_ID,
                        "MCART_BLAGO_APPLICATIONS_HR_RESP_POSITION_" . $row['ID'],
                        ""
                    ),
                    true
                );

                if (!empty($preHrRespPosition)) {
                    $result[$row['ID']]['position_hrs'] = array_keys($preHrRespPosition)[0];
                    $allPosition[] = $result[$row['ID']]['position_hrs'];
                }

                $preHrHeadPosition = json_decode(
                    \Bitrix\Main\Config\Option::get(
                        self::MODULE_ID,
                        "MCART_BLAGO_APPLICATIONS_HR_HEAD_POSITION_" . $row['ID'],
                        ""
                    ),
                    true
                );

                if (!empty($preHrHeadPosition)) {
                    $result[$row['ID']]['position_head'] = array_keys($preHrHeadPosition)[0];
                    $allPosition[] = $result[$row['ID']]['position_head'];
                }
            }

            $allPosition = array_values(array_unique($allPosition));

            $employees_hl_block_id = self::getEmployeesHlBlockId();
            if ($employees_hl_block_id <= 0) {
                throw new \Bitrix\Main\SystemException('Ошибка, попробуйте позже!');
            }

            $hlblock = HighloadBlockTable::getById($employees_hl_block_id)->fetch();
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $employeesEntityDataClass = $entity->getDataClass();

            $dbQuery = $employeesEntityDataClass::query()
                ->setSelect([
                    'ID',
                    'UF_USER',
                    'UF_XML_ID',
                    'UF_IS_MAIN',
                    'UF_STAFFING_POSITION',
                    'USER.XML_ID'
                ])
                ->whereIn('UF_STAFFING_POSITION', $allPosition)
                ->where('UF_IS_MAIN', '=', 1)
                ->registerRuntimeField(
                    'USER',
                    array(
                        'data_type' => 'Bitrix\Main\UserTable',
                        'reference' => array('=this.UF_USER' => 'ref.ID'),
                    )
                );
            $queryCollection = $dbQuery->exec();

            while ($row = $queryCollection->fetch()) {
                foreach ($result as $key => $value) {
                    if ($value['position_head'] == $row['UF_STAFFING_POSITION']) {
                        $result[$key]['head'] = $row['EMPLOYEES_USER_XML_ID'];
                    }

                    if ($value['position_hrs'] == $row['UF_STAFFING_POSITION']) {
                        $result[$key]['hrs'][] = $row['EMPLOYEES_USER_XML_ID'];
                    }
                }
            }

            $realResult = [];
            foreach ($result as $key => $value) {
                $realResult[] = [
                    'legal' => $value['legal'],
                    'head' => $value['head'],
                    'hrs' => $value['hrs'],
                ];
            }

            return $realResult;
        } catch (\Bitrix\Main\SystemException $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (\Error $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (RestException $e) {
            throw new RestException($e->getMessage(), $e->getErrorCode());
        }
    }

    public static function buhLeadGet($params, $start, $server)
    {
        try {
            if (
                !\Bitrix\Main\Loader::includeModule('highloadblock')
            ) {
                throw new \Bitrix\Main\SystemException('Ошибка, попробуйте позже!');
            }

            $buhHeadPosition = [];
            $preBuhHeadPosition = json_decode(
                \Bitrix\Main\Config\Option::get(
                    self::MODULE_ID,
                    "MCART_BLAGO_APPLICATIONS_BUH_LEAD_POSITION",
                    ""
                ),
                true
            );

            if (!empty($preBuhHeadPosition)) {
                $buhHeadPosition = $preBuhHeadPosition;
            }

            if (empty($buhHeadPosition)) {
                return false;
            }

            $employees_hl_block_id = self::getEmployeesHlBlockId();
            if ($employees_hl_block_id <= 0) {
                throw new \Bitrix\Main\SystemException('Ошибка, попробуйте позже!');
            }

            $hlblock = HighloadBlockTable::getById($employees_hl_block_id)->fetch();
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $employeesEntityDataClass = $entity->getDataClass();

            $dbQuery = $employeesEntityDataClass::query()
                ->setSelect([
                    'ID',
                    'UF_USER',
                    'UF_XML_ID',
                    'UF_IS_MAIN',
                    'UF_STAFFING_POSITION',
                    'USER.XML_ID'
                ])
                ->whereIn('UF_STAFFING_POSITION', array_keys($buhHeadPosition))
                ->where('UF_IS_MAIN', '=', 1)
                ->registerRuntimeField(
                    'USER',
                    array(
                        'data_type' => 'Bitrix\Main\UserTable',
                        'reference' => array('=this.UF_USER' => 'ref.ID'),
                    )
                );
            $queryCollection = $dbQuery->exec();

            if ($obQuery = $queryCollection->fetch()) {
                return $obQuery['EMPLOYEES_USER_XML_ID'];
            }

            return false;
        } catch (\Bitrix\Main\SystemException $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (\Error $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (RestException $e) {
            throw new RestException($e->getMessage(), $e->getErrorCode());
        }
    }

    public static function buhHeadGet($params, $start, $server)
    {
        try {
            if (
                !\Bitrix\Main\Loader::includeModule('highloadblock')
            ) {
                throw new \Bitrix\Main\SystemException('Ошибка, попробуйте позже!');
            }

            $buhHeadPosition = [];
            $preBuhHeadPosition = json_decode(
                \Bitrix\Main\Config\Option::get(
                    self::MODULE_ID,
                    "MCART_BLAGO_APPLICATIONS_BUH_HEAD_POSITION",
                    ""
                ),
                true
            );

            if (!empty($preBuhHeadPosition)) {
                $buhHeadPosition = $preBuhHeadPosition;
            }

            if (empty($buhHeadPosition)) {
                return false;
            }

            $employees_hl_block_id = self::getEmployeesHlBlockId();
            if ($employees_hl_block_id <= 0) {
                throw new \Bitrix\Main\SystemException('Ошибка, попробуйте позже!');
            }

            $hlblock = HighloadBlockTable::getById($employees_hl_block_id)->fetch();
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $employeesEntityDataClass = $entity->getDataClass();

            $dbQuery = $employeesEntityDataClass::query()
                ->setSelect([
                    'ID',
                    'UF_USER',
                    'UF_XML_ID',
                    'UF_IS_MAIN',
                    'UF_STAFFING_POSITION',
                    'USER.XML_ID'
                ])
                ->whereIn('UF_STAFFING_POSITION', array_keys($buhHeadPosition))
                ->where('UF_IS_MAIN', '=', 1)
                ->registerRuntimeField(
                    'USER',
                    array(
                        'data_type' => 'Bitrix\Main\UserTable',
                        'reference' => array('=this.UF_USER' => 'ref.ID'),
                    )
                );
            $queryCollection = $dbQuery->exec();

            if ($obQuery = $queryCollection->fetch()) {
                return $obQuery['EMPLOYEES_USER_XML_ID'];
            }

            return false;
        } catch (\Bitrix\Main\SystemException $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (\Error $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (RestException $e) {
            throw new RestException($e->getMessage(), $e->getErrorCode());
        }
    }

    public static function highloadblockElementAdd($params, $start, $server)
    {
        try {
            $hlblock = HighloadBlock::get($params);

            return HighloadBlockElement::add($hlblock['ID'], $params);
        } catch (\Bitrix\Main\SystemException $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (\Error $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (RestException $e) {
            throw new RestException($e->getMessage(), $e->getErrorCode());
        }
    }

    public static function highloadblockElementUpdate($params, $start, $server)
    {
        try {
            $hlblock = HighloadBlock::get($params);

            return HighloadBlockElement::update($hlblock['ID'], $params);
        } catch (\Bitrix\Main\SystemException $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (\Error $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (RestException $e) {
            throw new RestException($e->getMessage(), $e->getErrorCode());
        }
    }

    public static function highloadblockGetFields($params, $start, $server)
    {
        try {
            $hlblock = HighloadBlock::get($params);

            return HighloadBlockElement::getFields($hlblock['ID'], $params);
        } catch (\Bitrix\Main\SystemException $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (\Error $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (RestException $e) {
            throw new RestException($e->getMessage(), $e->getErrorCode());
        }
    }

    public static function highloadblockElementGet($params, $start, $server)
    {
        try {
            $hlblock = HighloadBlock::get($params);

            return HighloadBlockElement::get($hlblock['ID'], $params);
        } catch (\Bitrix\Main\SystemException $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (\Error $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (RestException $e) {
            throw new RestException($e->getMessage(), $e->getErrorCode());
        }
    }

    public static function highloadblockElementList($params, $start, $server)
    {
        try {
            $hlblock = HighloadBlock::get($params);

            return HighloadBlockElement::getList($hlblock['ID'], $params, $start);
        } catch (\Bitrix\Main\SystemException $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (\Error $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (RestException $e) {
            throw new RestException($e->getMessage(), $e->getErrorCode());
        }
    }

    public static function fileLists($params, $start, $server)
    {
        try {
            $result = [];
            foreach ($params['IDS'] as $key => $id) {
                $file = \CFile::GetByID($id)->fetch();
                $fileArray = \CFile::MakeFileArray($id);

                if ($file) {
                    $base64file = base64_encode(file_get_contents($fileArray['tmp_name']));

                    $result[$key] = $file;
                    $result[$key]['BASE64'] = $base64file;
                } else {
                    $result[$key] = false;
                }
            }

            return $result;
        } catch (\Bitrix\Main\SystemException $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (\Error $e) {
            throw new RestException($e->getMessage(), self::ERROR_INTERNAL);
        } catch (RestException $e) {
            throw new RestException($e->getMessage(), $e->getErrorCode());
        }
    }
}