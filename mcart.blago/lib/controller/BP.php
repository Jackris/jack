<?php

namespace Mcart\Blago\Controller;

use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;

class BP extends Controller
{
    private const MODULE_ID = "mcart.blago";
    private const CONFIG_SUFFIX = "MCART_BLAGO_BP_";

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function configureActions(): array
    {
        return [
            'initSign' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST,
                    ]),
                    new ActionFilter\Authentication(),
                    new ActionFilter\Csrf(),
                ],
            ],
            'initApprove' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST,
                    ]),
                    new ActionFilter\Authentication(),
                    new ActionFilter\Csrf(),
                ],
            ],
            'getData' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST,
                    ]),
                    new ActionFilter\Authentication(),
                    new ActionFilter\Csrf(),
                ],
            ],
            'sign' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST,
                    ]),
                    new ActionFilter\Authentication(),
                    new ActionFilter\Csrf(),
                ],
            ],
            'getDataHead' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST,
                    ]),
                    new ActionFilter\Authentication(),
                    new ActionFilter\Csrf(),
                ],
            ],
            'getDataApprove' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST,
                    ]),
                    new ActionFilter\Authentication(),
                    new ActionFilter\Csrf(),
                ],
            ],
            'setCause' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST,
                    ]),
                    new ActionFilter\Authentication(),
                    new ActionFilter\Csrf(),
                ],
            ],
            'setCauseRequest' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST,
                    ]),
                    new ActionFilter\Authentication(),
                    new ActionFilter\Csrf(),
                ],
            ],
            'getDataHeadUkep' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST,
                    ]),
                    new ActionFilter\Authentication(),
                    new ActionFilter\Csrf(),
                ],
            ],
            'initSignUkep' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST,
                    ]),
                    new ActionFilter\Authentication(),
                    new ActionFilter\Csrf(),
                ],
            ],
            'signHeadUkep' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST,
                    ]),
                    new ActionFilter\Authentication(),
                    new ActionFilter\Csrf(),
                ],
            ],
            'signUkep' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST,
                    ]),
                    new ActionFilter\Authentication(),
                    new ActionFilter\Csrf(),
                ],
            ],
            'getDocumentInfo' => [
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

    public function initApproveAction($taskId, $action,$responce=[])
    {
        try {
            if (
                !\Bitrix\Main\Loader::includeModule('bizproc') ||
                !\Bitrix\Main\Loader::includeModule('highloadblock') ||
                !\Bitrix\Main\Loader::includeModule('disk')
            ) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $user = $GLOBALS["USER"];
            $userId = $user->getId();

            if ((int)$taskId <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $dbTask = \CBPTaskService::GetList(
                [],
                [
                    "ID" => $taskId,
                    "USER_ID" => $user->getId(),
                    "USER_STATUS" => 0,
                ],
                false,
                false,
                [
                    "ID",
                    "WORKFLOW_ID",
                    "ACTIVITY",
                    "ACTIVITY_NAME",
                    "MODIFIED",
                    "OVERDUE_DATE",
                    "NAME",
                    "DESCRIPTION",
                    "PARAMETERS",
                    "USER_STATUS",
                ]
            );

            $task = $dbTask->fetch();

            if (!is_array($task)) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            if ((int)$task['PARAMETERS']['USER_ID'] <= 0  && isSet($task['PARAMETRS']['USERS'])) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

           
            $arEventParameters = [
                'USER_ID' => $user->getId(),
                'REAL_USER_ID' => $user->getId(),
                'USER_NAME' => $user->getFullName(),
                'ACTION' => $action,
            ];
            if($task['ACTIVITY']=='McartEstaffSign'){
                $arEventParameters['APPROVE']=true;
            }
            if($task['ACTIVITY']=='RequestInformationActivity'){
                if(!empty($responce)){
                    if(!empty($responce['VACANCY'])||$responce['NEED_CHANGE']){
                        $arEventParameters['RESPONCE']['VACANCY']=$responce['VACANCY'];
                        $arEventParameters['RESPONCE']['NEED_CHANGE']=$responce['NEED_CHANGE'];
                        $arEventParameters['RESPONCE']['VACANCY_NOT_EXIST']=$responce['VACANCY_NOT_EXIST'];
                    }else{
                        $arEventParameters['RESPONCE']['COMPENSATION_LIMIT']=$responce['COMPENSATION_LIMIT'];
                        $arEventParameters['RESPONCE']['COMPENSATION']=$responce['COMPENSATION'];
                        $arEventParameters['RESPONCE']['AWARDS']=$responce['AWARDS'];
                        $arEventParameters['RESPONCE']['SALARY']=$responce['SALARY'];
                    }

                    $arEventParameters['COMMENT']=$responce['COMMENT'];
                }
                // $arEventParameters['APPROVE']=true;
            }
            if($task['ACTIVITY']=='McartRequestInformationActivity'){
                if(!empty($responce)){
                    $arEventParameters['RESPONCE']=$responce;
                }
            }
            \CBPRuntime::SendExternalEvent($task['WORKFLOW_ID'], $task['ACTIVITY_NAME'], $arEventParameters);


            return [
                'taskId' => $taskId,
                'status' => 'task_close',
                'result' => $action,
            ];

        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    public function signAction($taskId, $signingRequestId, $pin)
    {
        try {
            if (
                !\Bitrix\Main\Loader::includeModule('bizproc') ||
                !\Bitrix\Main\Loader::includeModule('highloadblock') ||
                !\Bitrix\Main\Loader::includeModule('disk')
            ) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $user = $GLOBALS["USER"];
            $userId = $user->getId();

            if ((int)$taskId <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $dbTask = \CBPTaskService::GetList(
                [],
                [
                    "ID" => $taskId,
                    "USER_ID" => $user->getId(),
                    "USER_STATUS" => 0,
                ],
                false,
                false,
                [
                    "ID",
                    "WORKFLOW_ID",
                    "ACTIVITY",
                    "ACTIVITY_NAME",
                    "MODIFIED",
                    "OVERDUE_DATE",
                    "NAME",
                    "DESCRIPTION",
                    "PARAMETERS",
                    "USER_STATUS",
                ]
            );

            $task = $dbTask->fetch();

            if (!is_array($task)) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            if ((int)$task['PARAMETERS']['SIGN_ID'] <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $signatures_hl_block_id = $this->getSignaturesHlBlockId();
            if ($signatures_hl_block_id <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $hlblock = HighloadBlockTable::getById($signatures_hl_block_id)->fetch();
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $signaturesEntityDataClass = $entity->getDataClass();

            $arType = $this->getSignaturesTypeIdEmployeeHead();

            $arSign = $signaturesEntityDataClass::query()
                ->setSelect([
                    'ID',
                    'UF_FILE',
                    'UF_USER',
                    'UF_TYPE',
                    'UF_LINK_REP',
                ])
                ->where('ID', '=', $task['PARAMETERS']['SIGN_ID'])
                ->where('UF_USER', '=', $userId)
                ->whereIn('UF_TYPE', $arType)
                ->exec()
                ->fetch();

            if (!is_array($arSign)) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            if (!empty($arSign['UF_LINK_REP'])) {
                $result = \Mcart\HRLink\Document::agreeSignDocumentsNQES(
                    $userId,
                    $signingRequestId,
                    $arSign['UF_LINK_REP'],
                    $pin
                );

                if ($result['result'] == 1) {
                    $arStatusConfirming = $this->getSignaturesStatusConfirming();

                    $result = $signaturesEntityDataClass::update($task['PARAMETERS']['SIGN_ID'], [
                        'UF_STATUS' => $arStatusConfirming,
                    ]);
                    if ($result->isSuccess()) {
                        return [
                            'taskId' => $taskId,
                            'documentId' => $arSign['UF_LINK_REP'],
                        ];
                    } else {
                        throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR_SING'));
                    }
                } else {
                    throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR_SING'));
                }
            } else {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    public function initSignAction($taskId)
    {
        try {
            if (
                !\Bitrix\Main\Loader::includeModule('bizproc') ||
                !\Bitrix\Main\Loader::includeModule('highloadblock') ||
                !\Bitrix\Main\Loader::includeModule('disk')
            ) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $user = $GLOBALS["USER"];
            $userId = $user->getId();

            if ((int)$taskId <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $dbTask = \CBPTaskService::GetList(
                [],
                [
                    "ID" => $taskId,
                    "USER_ID" => $user->getId(),
                    "USER_STATUS" => 0,
                ],
                false,
                false,
                [
                    "ID",
                    "WORKFLOW_ID",
                    "ACTIVITY",
                    "ACTIVITY_NAME",
                    "MODIFIED",
                    "OVERDUE_DATE",
                    "NAME",
                    "DESCRIPTION",
                    "PARAMETERS",
                    "USER_STATUS",
                ],
            );

            $task = $dbTask->fetch();

            if (!is_array($task)) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            if ((int)$task['PARAMETERS']['SIGN_ID'] <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $signatures_hl_block_id = $this->getSignaturesHlBlockId();
            if ($signatures_hl_block_id <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $hlblock = HighloadBlockTable::getById($signatures_hl_block_id)->fetch();
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $signaturesEntityDataClass = $entity->getDataClass();

            $arType = $this->getSignaturesTypeIdEmployeeHead();

            $arSign = $signaturesEntityDataClass::query()
                ->setSelect([
                    'ID',
                    'UF_FILE',
                    'UF_USER',
                    'UF_TYPE',
                    'UF_LINK_REP',
                    'UF_EMP_XML_ID',
                ])
                ->registerRuntimeField('USER', [
                    'data_type' => \Bitrix\Main\UserTable::getEntity(),
                    'reference' => [
                        '=this.UF_USER' => 'ref.ID',
                    ],
                    'join_type' => 'inner',
                ])
                ->where('ID', '=', $task['PARAMETERS']['SIGN_ID'])
                ->where('UF_USER', '=', $userId)
                ->whereIn('UF_TYPE', $arType)
                ->exec()
                ->fetch();

            if (!is_array($arSign)) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            if (!empty($arSign['UF_LINK_REP']) && !empty($arSign['UF_EMP_XML_ID'])) {
                $resultInfo = \Mcart\HRLink\Document::getDocumentInfo($userId, $arSign['UF_LINK_REP']);
                if ($resultInfo['result'] == 1) {
                    if (is_array($resultInfo['document']['pendingSigningInfo']) && count($resultInfo['document']['pendingSigningInfo'])) {
                        foreach ($resultInfo['document']['pendingSigningInfo'] as $signingInfo) {
                            $resultEmployee = \Mcart\HRLink\User::searchEmployeeId($userId, $signingInfo['employeeId']);

                            if ($resultEmployee['result'] == 1) {
                                foreach ($resultEmployee['employee']['legalEntities'] as $employee) {
                                    if (
                                        $signingInfo['employeeId'] == $employee['employeeId']
                                        && $employee['externalId'] == $arSign['UF_EMP_XML_ID']
                                    ) {
                                        throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR_SING_START'));
                                    }
                                }
                            }

                        }
                    }
                }
                $arCertificate = \Mcart\HRLink\User::getCertificatesNQESEmployeeExternalId($userId, $arSign['UF_EMP_XML_ID']);

                if (!is_array($arCertificate) || empty($arCertificate['id'])) {
                    throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
                }

                $result = \Mcart\HRLink\Document::startSignDocumentsNQES(
                    $userId,
                    $arCertificate['id'],
                    $arSign['UF_LINK_REP'],
                );

                if ($result['result'] == 1) {
                    $signingRequestId = $result['signingRequestId'];
                    $arStatusConfirming = $this->getSignaturesStatusConfirming();

                    $result = $signaturesEntityDataClass::update($task['PARAMETERS']['SIGN_ID'], [
                        'UF_TASK_ID' => $taskId,
                        'UF_STATUS' => $arStatusConfirming,
                        'UF_SIGNING_REQUEST' => $signingRequestId,
                    ]);
                    if ($result->isSuccess()) {
                        return [
                            'taskId' => $taskId,
                            'signingRequestId' => $signingRequestId,
                        ];
                    }
                } else {
                    throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
                }
            } else {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    public function getDataAction($taskId)
    {
        try {
            if (
                !\Bitrix\Main\Loader::includeModule('bizproc') ||
                !\Bitrix\Main\Loader::includeModule('highloadblock') ||
                !\Bitrix\Main\Loader::includeModule('disk')
            ) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            return $this->getDataTask($taskId, $this->getSignaturesTypeIdEmployee(), 'EMPLOYEE');
        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    private function getFilesHlBlockId()
    {
        $ob = HighloadBlockTable::getList([
            'select' => ['ID'],
            'order' => ['ID' => 'ASC'],
            'filter' => [
                '=NAME' => 'Files',
            ],
            'limit' => 1,
        ]);

        if ($row = $ob->fetch()) {
            return $row['ID'];
        }

        return 0;
    }

    private function getSignaturesHlBlockId()
    {
        $ob = HighloadBlockTable::getList([
            'select' => ['ID'],
            'order' => ['ID' => 'ASC'],
            'filter' => [
                '=NAME' => 'Signatures',
            ],
            'limit' => 1,
        ]);

        if ($row = $ob->fetch()) {
            return $row['ID'];
        }

        return 0;
    }

    private function getEmployeesHlBlockId()
    {
        $ob = HighloadBlockTable::getList([
            'select' => ['ID'],
            'order' => ['ID' => 'ASC'],
            'filter' => [
                '=NAME' => 'Employees',
            ],
            'limit' => 1,
        ]);

        if ($row = $ob->fetch()) {
            return $row['ID'];
        }

        return 0;
    }

    private function getLegalEntityHlBlockId()
    {
        $ob = HighloadBlockTable::getList([
            'select' => ['ID'],
            'order' => ['ID' => 'ASC'],
            'filter' => [
                '=NAME' => 'LegalEntities',
            ],
            'limit' => 1,
        ]);

        if ($row = $ob->fetch()) {
            return $row['ID'];
        }

        return 0;
    }

    private function getSignaturesTypeIdEmployee()
    {
        $hl_block_id = $this->getSignaturesHlBlockId();
        global $USER_FIELD_MANAGER;
        $fields = $USER_FIELD_MANAGER->getUserFieldsWithReadyData(
            'HLBLOCK_' . $hl_block_id,
            [],
            LANGUAGE_ID
        );

        $obEnum = new \CUserFieldEnum();
        $rsEnum = $obEnum->GetList([], ['USER_FIELD_ID' => $fields['UF_TYPE']['ID']]);
        while ($arEnum = $rsEnum->GetNext()) {
            if ($arEnum['XML_ID'] === 'EMPLOYEE') {
                return $arEnum['ID'];
            }
        }

        return 0;
    }

    private function getSignaturesTypeIdHead()
    {
        $hl_block_id = $this->getSignaturesHlBlockId();
        global $USER_FIELD_MANAGER;
        $fields = $USER_FIELD_MANAGER->getUserFieldsWithReadyData(
            'HLBLOCK_' . $hl_block_id,
            [],
            LANGUAGE_ID
        );

        $obEnum = new \CUserFieldEnum();
        $rsEnum = $obEnum->GetList(
            [],
            [
                'USER_FIELD_ID' => $fields['UF_TYPE']['ID'],
                'XML_ID' => 'HEAD_N',
            ]
        );
        while ($arEnum = $rsEnum->GetNext()) {
            return $arEnum['ID'];
        }

        return 0;
    }


    private function getSignaturesTypeIdHeadUkep()
    {
        $hl_block_id = $this->getSignaturesHlBlockId();
        global $USER_FIELD_MANAGER;
        $fields = $USER_FIELD_MANAGER->getUserFieldsWithReadyData(
            'HLBLOCK_' . $hl_block_id,
            [],
            LANGUAGE_ID
        );

        $obEnum = new \CUserFieldEnum();
        $rsEnum = $obEnum->GetList(
            [],
            [
                'USER_FIELD_ID' => $fields['UF_TYPE']['ID'],
                'XML_ID' => 'HEAD_K',
            ]
        );
        while ($arEnum = $rsEnum->GetNext()) {
            return $arEnum['ID'];
        }

        return 0;
    }

    private function getSignaturesTypeIdHeads()
    {
        $arResult = [];
        $hl_block_id = $this->getSignaturesHlBlockId();
        global $USER_FIELD_MANAGER;
        $fields = $USER_FIELD_MANAGER->getUserFieldsWithReadyData(
            'HLBLOCK_' . $hl_block_id,
            [],
            LANGUAGE_ID
        );

        $obEnum = new \CUserFieldEnum();
        $rsEnum = $obEnum->GetList(
            [],
            [
                'USER_FIELD_ID' => $fields['UF_TYPE']['ID'],
                'XML_ID' => ['HEAD_K', 'HEAD_N'],
            ]
        );
        while ($arEnum = $rsEnum->GetNext()) {
            $arResult[] = $arEnum['ID'];
        }

        return $arResult;
    }

    private function getSignaturesTypeIdEmployeeHead()
    {
        $arResult = [];
        $hl_block_id = $this->getSignaturesHlBlockId();
        global $USER_FIELD_MANAGER;
        $fields = $USER_FIELD_MANAGER->getUserFieldsWithReadyData(
            'HLBLOCK_' . $hl_block_id,
            [],
            LANGUAGE_ID
        );

        $obEnum = new \CUserFieldEnum();
        $rsEnum = $obEnum->GetList(
            [],
            [
                'USER_FIELD_ID' => $fields['UF_TYPE']['ID'],
                'XML_ID' => ['EMPLOYEE', 'HEAD_N'],
            ]
        );
        while ($arEnum = $rsEnum->GetNext()) {
            $arResult[] = $arEnum['ID'];
        }

        return $arResult;
    }


    private function getSignaturesStatus()
    {
        $statuses = [];

        $hl_block_id = $this->getSignaturesHlBlockId();

        global $USER_FIELD_MANAGER;
        $fields = $USER_FIELD_MANAGER->getUserFieldsWithReadyData(
            'HLBLOCK_' . $hl_block_id,
            [],
            LANGUAGE_ID
        );

        $obEnum = new \CUserFieldEnum();
        $rsEnum = $obEnum->GetList([], ['USER_FIELD_ID' => $fields['UF_STATUS']['ID']]);
        while ($arEnum = $rsEnum->GetNext()) {
            $statuses[$arEnum['ID']] = $arEnum['XML_ID'];
        }

        return $statuses;
    }

    private function getSignaturesStatusConfirming()
    {
        $hl_block_id = $this->getSignaturesHlBlockId();

        global $USER_FIELD_MANAGER;
        $fields = $USER_FIELD_MANAGER->getUserFieldsWithReadyData(
            'HLBLOCK_' . $hl_block_id,
            [],
            LANGUAGE_ID
        );

        $obEnum = new \CUserFieldEnum();
        $rsEnum = $obEnum->GetList([], [
            'USER_FIELD_ID' => $fields['UF_STATUS']['ID'],
            'XML_ID' => 'CONFIRMING',
        ]);
        while ($arEnum = $rsEnum->GetNext()) {
            return $arEnum['ID'];
        }

        return 0;
    }

    public function getDataHeadAction($taskId)
    {
        try {
            if (
                !\Bitrix\Main\Loader::includeModule('bizproc') ||
                !\Bitrix\Main\Loader::includeModule('highloadblock') ||
                !\Bitrix\Main\Loader::includeModule('disk')
            ) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            return $this->getDataTask($taskId, $this->getSignaturesTypeIdHead(), 'HEAD_N');
        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    public function getDataHeadUkepAction($taskId)
    {
        try {
            if (
                !\Bitrix\Main\Loader::includeModule('bizproc') ||
                !\Bitrix\Main\Loader::includeModule('highloadblock') ||
                !\Bitrix\Main\Loader::includeModule('disk')
            ) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            return $this->getDataTask($taskId, $this->getSignaturesTypeIdHeadUkep(), 'HEAD_K');
        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    private function getDataTask(int $taskId, int $typeId, string $type): array
    {
        $user = $GLOBALS["USER"];
        $userId = $user->getId();

        if ((int)$taskId <= 0) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
        }

        $dbTask = \CBPTaskService::GetList(
            [],
            [
                "ID" => $taskId,
                "USER_ID" => $user->getId(),
                "USER_STATUS" => 0,
            ],
            false,
            false,
            [
                "ID",
                "WORKFLOW_ID",
                "ACTIVITY",
                "ACTIVITY_NAME",
                "MODIFIED",
                "OVERDUE_DATE",
                "NAME",
                "DESCRIPTION",
                "PARAMETERS",
                "USER_STATUS",
            ]
        );
        $task = $dbTask->fetch();

        if (!is_array($task)) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
        }
        
        $runtime = \CBPRuntime::GetRuntime();

        $workflow = $runtime->GetWorkflow($task['WORKFLOW_ID'], true);
        if (!$workflow) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
        }

        $activity = $workflow->GetActivityByName($task['ACTIVITY_NAME']);
        if (!$activity) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
        }



        if ((int)$task['PARAMETERS']['SIGN_ID'] <= 0) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
        }

        $signatures_hl_block_id = $this->getSignaturesHlBlockId();
        if ($signatures_hl_block_id <= 0) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
        }
        $hlblock = HighloadBlockTable::getById($signatures_hl_block_id)->fetch();
        $entity = HighloadBlockTable::compileEntity($hlblock);
        $signaturesEntityDataClass = $entity->getDataClass();

        $signOb = $signaturesEntityDataClass::query()
            ->setSelect([
                'ID',
                'UF_FILE',
                'UF_USER',
                'UF_TYPE',
                'UF_LINK_REP',
                'UF_EMP_XML_ID',
            ])
            ->where('ID', '=', $task['PARAMETERS']['SIGN_ID'])
            ->where('UF_USER', '=', $userId)
            ->where('UF_TYPE', '=', $typeId)
            ->exec()
            ->fetch();
        if (!is_array($signOb)) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
        }

        $files_hl_block_id = $this->getFilesHlBlockId();
        if ($files_hl_block_id <= 0) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
        }

        $hlblock = HighloadBlockTable::getById($files_hl_block_id)->fetch();
        $entity = HighloadBlockTable::compileEntity($hlblock);
        $filesEntityDataClass = $entity->getDataClass();

        $fileOb = $filesEntityDataClass::query()
            ->setSelect([
                'ID',
                'UF_FILE',
                'UF_DATE',
            ])
            ->where('ID', '=', $signOb['UF_FILE'])
            ->exec()
            ->fetch();
        if (!is_array($fileOb)) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
        }

        $file = \CFile::GetFileArray($fileOb['UF_FILE']);
        if (!is_array($file)) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
        }

//        $resultCheck = self::checkSing($userId, $signOb['UF_LINK_REP'], $type, $signOb['UF_EMP_XML_ID']);
//
//        if (!empty($resultCheck['RESULT'])) {
//            if ($resultCheck['DATE']) {
//                $result = $signaturesEntityDataClass::update($task['PARAMETERS']['SIGN_ID'], [
//                    'UF_DATE_SING' => $resultCheck['DATE'],
//                ]);
//                if ($result->isSuccess()) {
//                    return self::finishTask($user, $task, $resultCheck);
//                }
//            }
//        }

        $fileSizeType = 'кб';
        $fileSize = $file['FILE_SIZE'] / 1024;
        if (round($fileSize / 1024, 2) > 1024) {
            $fileSize /= 1024;
            $fileSizeType = 'мб';
        }
        $fileSize = round($fileSize, 2);

        return [
            'status' => 'task_open',
            'element' => [
                'link' => $activity->ApplicationLink,
                'name' => $activity->ApplicationName,
            ],
            'document' => [
                'link' => $file['SRC'],
                'name' => $file['ORIGINAL_NAME'],
                'size' => $fileSize,
                'sizeType' => $fileSizeType,
                'date' => $fileOb['UF_DATE'] ? $fileOb['UF_DATE']->format('d.m.Y') : '',
            ],
        ];
    }

    public function getDataApproveAction($taskId, $action)
    {
        try {
            if (
                !\Bitrix\Main\Loader::includeModule('bizproc') ||
                !\Bitrix\Main\Loader::includeModule('highloadblock') ||
                !\Bitrix\Main\Loader::includeModule('disk')
            ) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            return $this->getTask($taskId, $action);
        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    private function getTask(int $taskId,$action): array
    {
        $user = $GLOBALS["USER"];
        $userId = $user->getId();

        if ((int)$taskId <= 0) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
        }

        $dbTask = \CBPTaskService::GetList(
            [],
            [
                "ID" => $taskId,
                "USER_ID" => $user->getId(),
                "USER_STATUS" => 0,
            ],
            false,
            false,
            [
                "ID",
                "WORKFLOW_ID",
                "ACTIVITY",
                "ACTIVITY_NAME",
                "MODIFIED",
                "OVERDUE_DATE",
                "NAME",
                "DESCRIPTION",
                "PARAMETERS",
                "USER_STATUS",
            ]
        );
        $task = $dbTask->fetch();

        if (!is_array($task)) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
        }
        
        $runtime = \CBPRuntime::GetRuntime();

        $workflow = $runtime->GetWorkflow($task['WORKFLOW_ID'], true);
        if (!$workflow) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
        }

        $activity = $workflow->GetActivityByName($task['ACTIVITY_NAME']);
        if (!$activity) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
        }



        if ((int)$task['PARAMETERS']['USER_ID'] <= 0 && isSet($task['PARAMETERS']['USERS'])) {
            throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
        }

        $element = [];

        if ($action === "requestInfo") {
            $element = [
                'name' => $activity->Name,
                'description' => $activity->Name,
                'requestInfo' => $task["PARAMETERS"]["REQUEST"],
            ];
        } else {
            $element = [
                'link' => $activity->ApplicationLink,
                'name' => $activity->ApplicationName,
            ];
        }

        return [
            'status' => 'task_open',
            'element' => $element,
        ];
    }

    
    public function setCauseRequestAction($taskId, $cause, $action)
    {
        try {
            if (
                !\Bitrix\Main\Loader::includeModule('bizproc') ||
                !\Bitrix\Main\Loader::includeModule('highloadblock')
            ) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $user = $GLOBALS["USER"];
            $userId = $user->getId();

            if ((int)$taskId <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $dbTask = \CBPTaskService::GetList(
                [],
                [
                    "ID" => $taskId,
                    "USER_ID" => $user->getId(),
                    "USER_STATUS" => 0,
                ],
                false,
                false,
                [
                    "ID",
                    "WORKFLOW_ID",
                    "ACTIVITY",
                    "ACTIVITY_NAME",
                    "MODIFIED",
                    "OVERDUE_DATE",
                    "NAME",
                    "DESCRIPTION",
                    "PARAMETERS",
                    "USER_STATUS",
                ]
            );
            $task = $dbTask->fetch();

            if (!is_array($task)) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $arEventParameters = [
                'USER_ID' => $user->getId(),
                'REAL_USER_ID' => $user->getId(),
                'USER_NAME' => $user->getFullName(),
                'COMMENT' => isset($cause) ? trim($cause) : '',
                'ACTION' => $action,
            ];

            if (empty($arEventParameters['COMMENT'])) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR_CAUSE_COMMENT'));
            }

            $runtime = \CBPRuntime::GetRuntime();

            $workflow = $runtime->GetWorkflow($task['WORKFLOW_ID'], true);
            if (!$workflow) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $activity = $workflow->GetActivityByName($task['ACTIVITY_NAME']);
            if (!$activity) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            if ((int)$task['PARAMETERS']['USER_ID'] <= 0 && isSet($task['PARAMETERS']['USERS'])) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            \CBPRuntime::SendExternalEvent($task['WORKFLOW_ID'], $task['ACTIVITY_NAME'], $arEventParameters);
            
            return [
                'taskId' => $taskId,
                'result' => 'success',
            ];

        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    public function setCauseAction($taskId, $cause, $action)
    {
        try {
            if (
                !\Bitrix\Main\Loader::includeModule('bizproc') ||
                !\Bitrix\Main\Loader::includeModule('highloadblock')
            ) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $user = $GLOBALS["USER"];
            $userId = $user->getId();

            if ((int)$taskId <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $dbTask = \CBPTaskService::GetList(
                [],
                [
                    "ID" => $taskId,
                    "USER_ID" => $user->getId(),
                    "USER_STATUS" => 0,
                ],
                false,
                false,
                [
                    "ID",
                    "WORKFLOW_ID",
                    "ACTIVITY",
                    "ACTIVITY_NAME",
                    "MODIFIED",
                    "OVERDUE_DATE",
                    "NAME",
                    "DESCRIPTION",
                    "PARAMETERS",
                    "USER_STATUS",
                ]
            );
            $task = $dbTask->fetch();

            if (!is_array($task)) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $arEventParameters = [
                'USER_ID' => $user->getId(),
                'REAL_USER_ID' => $user->getId(),
                'USER_NAME' => $user->getFullName(),
                'COMMENT' => isset($cause) ? trim($cause) : '',
                'ACTION' => $action,
            ];

            if (empty($arEventParameters['COMMENT'])) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR_CAUSE_COMMENT'));
            }

            $runtime = \CBPRuntime::GetRuntime();

            $workflow = $runtime->GetWorkflow($task['WORKFLOW_ID'], true);
            if (!$workflow) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $activity = $workflow->GetActivityByName($task['ACTIVITY_NAME']);
            if (!$activity) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }
            if($task['ACTIVITY']=='McartEstaffSign'){
                $arEventParameters['APPROVE']=false;
                \CBPRuntime::SendExternalEvent($task['WORKFLOW_ID'], $task['ACTIVITY_NAME'], $arEventParameters);


                return [
                    'taskId' => $taskId,
                    'result' => 'success',
                ];

            }
            if($task['ACTIVITY']=='RequestInformationActivity'){
              //  $arEventParameters['APPROVE']=false;
              //  \CBPRuntime::SendExternalEvent($task['WORKFLOW_ID'], $task['ACTIVITY_NAME'], $arEventParameters);
              //  $activity->Unsubscribe($activity);
              //  $activity->closeActivity();
                $arEventParameters['APPROVE']=false;
                \CBPRuntime::SendExternalEvent($task['WORKFLOW_ID'], $task['ACTIVITY_NAME'], $arEventParameters);
                return [
                    'taskId' => $taskId,
                    'result' => 'success',
                ];

            }

            if ((int)$task['PARAMETERS']['SIGN_ID'] <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }
            
            $signatures_hl_block_id = $this->getSignaturesHlBlockId();
            if ($signatures_hl_block_id <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $hlblock = HighloadBlockTable::getById($signatures_hl_block_id)->fetch();
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $signaturesEntityDataClass = $entity->getDataClass();

            $arType = $this->getSignaturesTypeIdHeads();

            $arSign = $signaturesEntityDataClass::query()
                ->setSelect([
                    'ID',
                    'UF_FILE',
                    'UF_USER',
                    'UF_TYPE',
                    'UF_LINK_REP',
                ])
                ->where('ID', '=', $task['PARAMETERS']['SIGN_ID'])
                ->where('UF_USER', '=', $userId)
                ->whereIn('UF_TYPE', $arType)
                ->exec()
                ->fetch();

            if (!is_array($arSign)) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            if (!empty($arSign['UF_LINK_REP'])) {
                $result = \Mcart\HRLink\Document::rejectSigningDocuments(
                    $userId,
                    $arEventParameters['COMMENT'],
                    $arSign['UF_LINK_REP']
                );

                if ($result['result'] == 1) {
                    \CBPRuntime::SendExternalEvent($task['WORKFLOW_ID'], $task['ACTIVITY_NAME'], $arEventParameters);

                    return [
                        'taskId' => $taskId,
                        'result' => 'success',
                    ];
                } else {
                    throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR_CAUSE'));
                }
            }else {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    public function initSignUkepAction($taskId)
    {
        try {
            if (
                !\Bitrix\Main\Loader::includeModule('bizproc') ||
                !\Bitrix\Main\Loader::includeModule('highloadblock') ||
                !\Bitrix\Main\Loader::includeModule('disk')
            ) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $user = $GLOBALS["USER"];
            $userId = $user->getId();

            if ((int)$taskId <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $dbTask = \CBPTaskService::GetList(
                [],
                [
                    "ID" => $taskId,
                    "USER_ID" => $user->getId(),
                    "USER_STATUS" => 0,
                ],
                false,
                false,
                [
                    "ID",
                    "WORKFLOW_ID",
                    "ACTIVITY",
                    "ACTIVITY_NAME",
                    "MODIFIED",
                    "OVERDUE_DATE",
                    "NAME",
                    "DESCRIPTION",
                    "PARAMETERS",
                    "USER_STATUS",
                ],
            );

            $task = $dbTask->fetch();

            if (!is_array($task)) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }


            if ((int)$task['PARAMETERS']['SIGN_ID'] <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $signatures_hl_block_id = $this->getSignaturesHlBlockId();
            if ($signatures_hl_block_id <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $hlblock = HighloadBlockTable::getById($signatures_hl_block_id)->fetch();
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $signaturesEntityDataClass = $entity->getDataClass();

            $arType = $this->getSignaturesTypeIdHeadUkep();

            $arSign = $signaturesEntityDataClass::query()
                ->setSelect([
                    'ID',
                    'UF_FILE',
                    'UF_USER',
                    'UF_TYPE',
                    'UF_LINK_REP',
                ])
                ->where('ID', '=', $task['PARAMETERS']['SIGN_ID'])
                ->where('UF_USER', '=', $userId)
                ->whereIn('UF_TYPE', $arType)
                ->exec()
                ->fetch();

            if (!is_array($arSign)) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            if (!empty($arSign['UF_LINK_REP'])) {
                $result = \Mcart\HRLink\Document::getConvertedFile(
                    $userId,
                    $arSign['UF_LINK_REP'],
                );

                if ($result) {
                    return [
                        'taskId' => $taskId,
                        'dataFile' => $result,
                    ];
                } else {
                    throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
                }

            } else {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    public function signHeadUkepAction($taskId, $sign, $action)
    {
        try {
            if (
                !\Bitrix\Main\Loader::includeModule('bizproc') ||
                !\Bitrix\Main\Loader::includeModule('highloadblock') ||
                !\Bitrix\Main\Loader::includeModule('disk')
            ) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $user = $GLOBALS["USER"];
            $userId = $user->getId();

            if ((int)$taskId <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $dbTask = \CBPTaskService::GetList(
                [],
                [
                    "ID" => $taskId,
                    "USER_ID" => $user->getId(),
                    "USER_STATUS" => 0,
                ],
                false,
                false,
                [
                    "ID",
                    "WORKFLOW_ID",
                    "ACTIVITY",
                    "ACTIVITY_NAME",
                    "MODIFIED",
                    "OVERDUE_DATE",
                    "NAME",
                    "DESCRIPTION",
                    "PARAMETERS",
                    "USER_STATUS",
                ]
            );
            $task = $dbTask->fetch();

            if (!is_array($task)) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            if ((int)$task['PARAMETERS']['SIGN_ID'] <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $signatures_hl_block_id = $this->getSignaturesHlBlockId();
            if ($signatures_hl_block_id <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $hlblock = HighloadBlockTable::getById($signatures_hl_block_id)->fetch();
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $signaturesEntityDataClass = $entity->getDataClass();

            $arType = $this->getSignaturesTypeIdHeadUkep();

            $arSign = $signaturesEntityDataClass::query()
                ->setSelect([
                    'ID',
                    'UF_FILE',
                    'UF_USER',
                    'UF_TYPE',
                    'UF_LINK_REP',
                ])
                ->where('ID', '=', $task['PARAMETERS']['SIGN_ID'])
                ->where('UF_USER', '=', $userId)
                ->whereIn('UF_TYPE', $arType)
                ->exec()
                ->fetch();

            if (!is_array($arSign)) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            if (!empty($arSign['UF_LINK_REP'])) {
//                $result = \Mcart\HRLink\Document::agreeSignDocumentsQES(
//                    $userId,
//                    $arSign['UF_LINK_REP'],
//                    $sign
//                );


                $result = \Mcart\HRLink\Document::agreeSignDocumentsQESv2(
                    $userId,
                    $arSign['UF_LINK_REP'],
                    $sign
                );

                if ($result['result'] == 1) {
                    $result = $signaturesEntityDataClass::update($task['PARAMETERS']['SIGN_ID'], [
                        'UF_DATE_SING' => date('d.m.Y H:i:s'),
                    ]);

                    if ($result->isSuccess()) {
                        $arEventParameters = [
                            'USER_ID' => $user->getId(),
                            'REAL_USER_ID' => $user->getId(),
                            'USER_NAME' => $user->getFullName(),
                            'ACTION' => $action,
                        ];

                        \CBPRuntime::SendExternalEvent($task['WORKFLOW_ID'], $task['ACTIVITY_NAME'], $arEventParameters);

                        return [
                            'taskId' => $taskId,
                            'result' => 'success',
                        ];
                    } else {
                        throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR_SING'));
                    }
                } else {
                    throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR_SING'));
                }
            } else {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    public function signUkepAction($taskId, $sign)
    {
        try {
            if (
                !\Bitrix\Main\Loader::includeModule('bizproc') ||
                !\Bitrix\Main\Loader::includeModule('highloadblock') ||
                !\Bitrix\Main\Loader::includeModule('disk')
            ) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $user = $GLOBALS["USER"];
            $userId = $user->getId();

            if ((int)$taskId <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $dbTask = \CBPTaskService::GetList(
                [],
                [
                    "ID" => $taskId,
                    "USER_ID" => $user->getId(),
                    "USER_STATUS" => 0,
                ],
                false,
                false,
                [
                    "ID",
                    "WORKFLOW_ID",
                    "ACTIVITY",
                    "ACTIVITY_NAME",
                    "MODIFIED",
                    "OVERDUE_DATE",
                    "NAME",
                    "DESCRIPTION",
                    "PARAMETERS",
                    "USER_STATUS",
                ]
            );
            $task = $dbTask->fetch();

            if (!is_array($task)) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            if ((int)$task['PARAMETERS']['SIGN_ID'] <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $signatures_hl_block_id = $this->getSignaturesHlBlockId();
            if ($signatures_hl_block_id <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $hlblock = HighloadBlockTable::getById($signatures_hl_block_id)->fetch();
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $signaturesEntityDataClass = $entity->getDataClass();

            $arType = $this->getSignaturesTypeIdHeadUkep();

            $arSign = $signaturesEntityDataClass::query()
                ->setSelect([
                    'ID',
                    'UF_FILE',
                    'UF_USER',
                    'UF_TYPE',
                    'UF_LINK_REP',
                ])
                ->where('ID', '=', $task['PARAMETERS']['SIGN_ID'])
                ->where('UF_USER', '=', $userId)
                ->whereIn('UF_TYPE', $arType)
                ->exec()
                ->fetch();

            if (!is_array($arSign)) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            if (!empty($arSign['UF_LINK_REP'])) {
//                $result = \Mcart\HRLink\Document::agreeSignDocumentsQES(
//                    $userId,
//                    $arSign['UF_LINK_REP'],
//                    $sign
//                );


                $result = \Mcart\HRLink\Document::agreeSignDocumentsQESv2(
                    $userId,
                    $arSign['UF_LINK_REP'],
                    $sign
                );

                if ($result['result'] == 1) {
                    $result = $signaturesEntityDataClass::update($task['PARAMETERS']['SIGN_ID'], [
                        'UF_DATE_SING' => date('d.m.Y H:i:s'),
                    ]);

                    if ($result->isSuccess()) {
                        $arEventParameters = [
                            'USER_ID' => $user->getId(),
                            'REAL_USER_ID' => $user->getId(),
                            'USER_NAME' => $user->getFullName(),
                        ];

                        \CBPRuntime::SendExternalEvent($task['WORKFLOW_ID'], $task['ACTIVITY_NAME'], $arEventParameters);

                        return [
                            'taskId' => $taskId,
                            'result' => 'success',
                        ];
                    } else {
                        throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR_SING'));
                    }
                } else {
                    throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR_SING'));
                }
            } else {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    public function getDocumentInfoAction($signId)
    {
        try {
            if (
                !\Bitrix\Main\Loader::includeModule('bizproc') ||
                !\Bitrix\Main\Loader::includeModule('highloadblock') ||
                !\Bitrix\Main\Loader::includeModule('disk')
            ) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $user = $GLOBALS["USER"];
            $userId = $user->getId();

            if ((int)$signId <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $signatures_hl_block_id = $this->getSignaturesHlBlockId();
            if ($signatures_hl_block_id <= 0) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }

            $hlblock = HighloadBlockTable::getById($signatures_hl_block_id)->fetch();
            $entity = HighloadBlockTable::compileEntity($hlblock);
            $signaturesEntityDataClass = $entity->getDataClass();

            $arStatus = $this->getSignaturesStatus();

            $arSign = $signaturesEntityDataClass::query()
                ->setSelect([
                    'ID',
                    'UF_USER',
                    'UF_STATUS',
                ])
                ->where('ID', '=', $signId)
                ->where('UF_USER', '=', $userId)
                ->exec()
                ->fetch();

            if (!is_array($arSign)) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            }
            if ($arStatus[$arSign['UF_STATUS']] == 'SUCCEEDED') {
                return [
                    'result' => 'success',
                ];

            } elseif ($arStatus[$arSign['UF_STATUS']] == 'WRONG_CODE') {
                return [
                    'result' => 'error',
                    'message' => Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR_CODE'),
                ];
            } elseif ($arStatus[$arSign['UF_STATUS']] == 'FAILED') {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR'));
            } elseif ($arStatus[$arSign['UF_STATUS']] == 'EXPIRED') {
                throw new \Bitrix\Main\SystemException(Loc::getMessage(self::CONFIG_SUFFIX . 'ERROR_SING_EXPIRED'));
            }

            return [
                'result' => 'confirming',
            ];
        } catch (\Bitrix\Main\SystemException $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        } catch (\Error $e) {
            $this->addError(new Error($e->getMessage()));

            return true;
        }
    }

    private static function checkSing(int $userId, string $documentId, string $type, string $externalId): array
    {
        $result = [];
        $resultInfo = \Mcart\HRLink\Document::getDocumentInfo($userId, $documentId);

        if ($resultInfo['result'] == 1) {
            switch ($type) {
                case 'HEAD_K':
                    if ($headManager = $resultInfo['document']['headManager']) {
                        if ($headManager['madeDecision'] == 1) {
                            if ($headManager['signedDate']) {
                                $result = [
                                    'DATE' => \CRestUtil::unConvertDateTime($headManager['signedDate']),
                                    'RESULT' => 'yes_approve',
                                ];
                            } elseif ($headManager['rejectedDate']) {
                                $result = [
                                    'DATE' => \CRestUtil::unConvertDateTime($headManager['rejectedDate']),
                                    'COMMENT' => $headManager['rejectionComment'],
                                    'RESULT' => 'reject',
                                ];
                            }
                        }
                    }
                    break;

                case 'HEAD_N':
                    foreach ($resultInfo['document']['employees'] as $employee) {
                        if ($employee['externalId'] == $externalId && $employee['madeDecision'] == 1) {
                            if ($employee['signedDate']) {
                                $result = [
                                    'DATE' => \CRestUtil::unConvertDateTime($employee['signedDate']),
                                    'RESULT' => 'yes_approve',
                                ];
                            } elseif ($employee['rejectedDate']) {
                                $result = [
                                    'DATE' => \CRestUtil::unConvertDateTime($employee['rejectedDate']),
                                    'COMMENT' => $employee['rejectionComment'],
                                    'RESULT' => 'reject',
                                ];
                            }
                        }
                    }
                    break;

                case 'EMPLOYEE':
                    foreach ($resultInfo['document']['employees'] as $employee) {
                        if ($employee['externalId'] == $externalId && $employee['madeDecision'] == 1) {
                            if ($employee['signedDate']) {
                                $result = [
                                    'DATE' => \CRestUtil::unConvertDateTime($employee['signedDate']),
                                    'RESULT' => 'yes_approve',
                                ];
                            } elseif ($employee['rejectedDate']) {
                                $result = [
                                    'DATE' => \CRestUtil::unConvertDateTime($employee['rejectedDate']),
                                    'RESULT' => 'reject',
                                ];
                            }
                        }
                    }
                    break;
            }
        }

        return $result;
    }

    private static function finishTask(object $user, array $task, array $result): array
    {
        switch ($task['ACTIVITY']) {
            case 'McartSigningDocument':
            case 'McartUkepSigningDocument':
                if ($result['RESULT'] == 'reject') {
                    return [
                        'status' => 'task_emp_reject',
                    ];
                }

                $arEventParameters = [
                    'USER_ID' => $user->getId(),
                    'REAL_USER_ID' => $user->getId(),
                    'USER_NAME' => $user->getFullName(),
                ];

                break;

            case 'McartHeadSigningDocument':
            case 'McartHeadUkepSigningDocument	':
                $arEventParameters = [
                    'USER_ID' => $user->getId(),
                    'REAL_USER_ID' => $user->getId(),
                    'USER_NAME' => $user->getFullName(),
                    'ACTION' => $result['RESULT'],
                ];

                if (!empty($result['COMMENT'])) {
                    $arEventParameters['COMMENT'] = $result['COMMENT'];
                }

                break;
        }

        \CBPRuntime::SendExternalEvent($task['WORKFLOW_ID'], $task['ACTIVITY_NAME'], $arEventParameters);


        return [
            'status' => 'task_close',
            'result' => $result['RESULT'],
        ];
    }
}