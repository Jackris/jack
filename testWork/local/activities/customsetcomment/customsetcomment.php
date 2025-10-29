<?php

use Bitrix\Bizproc\Activity\PropertiesDialog;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class CBPCustomSetComment extends CBPActivity
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->arProperties = [
            'commentText' => null,
            'dealId' => null,
            'errorMessage' => '',
        ];
    }

    public function addCommentToDeal(): void
    {
        try {
            $commentText = $this->commentText;
            $dealId = $this->dealId;

            if (empty($commentText)) {
                throw new Exception('Не указан комментарий');
            }
            if (empty($dealId)) {
                throw new Exception('Не указан ID сделки');
            }

            $entityDeal = new \CCrmDeal(false);
            $resultFields = ['COMMENTS' => $commentText];
            if ($entityDeal->Update($dealId, $resultFields, true, true)) {
                $checkExceptions = $entityDeal->GetCheckExceptions();
                $errorMessage = $entityDeal->LAST_ERROR;
                throw new Exception('Не указан ID сделки');
            }
        } catch (Exception $e) {
            $this->writeToTrackingService($e->getMessage(), 0, CBPTrackingType::Error);
            $this->errorMessage = $e->getMessage();
        }
    }

    public static function getPropertiesDialog(
        $documentType,
        $activityName,
        $arWorkflowTemplate,
        $arWorkflowParameters,
        $arWorkflowVariables,
        $arCurrentValues = null,
        $formName = '',
        $popupWindow = null,
        $siteId = ''
    ) {
        if (!CModule::IncludeModule('crm')) {
            return '';
        }

        $dialog = new PropertiesDialog(__FILE__, [
            'documentType' => $documentType,
            'activityName' => $activityName,
            'workflowTemplate' => $arWorkflowTemplate,
            'workflowParameters' => $arWorkflowParameters,
            'workflowVariables' => $arWorkflowVariables,
            'currentValues' => $arCurrentValues,
            'formName' => $formName,
            'siteId' => $siteId,
        ]);
        $dialog->setMap(static::getPropertiesMap($documentType));

        return $dialog;
    }

    protected static function getPropertiesMap(array $documentType, array $context = []): array
    {
        return [
            'commentText' => [
                'Name' => GetMessage('CUSTOM_COMMENT_NAME_FIELD'),
                'FieldName' => 'commentText',
                'Type' => 'string',
            ],
            'dealId' => [
                'Name' => GetMessage('CUSTOM_COMMENT_DEAL_ID'),
                'FieldName' => 'dealId',
                'Type' => 'int',
            ],
        ];
    }

    public static function GetPropertiesDialogValues(
        $documentType,
        $activityName,
        &$arWorkflowTemplate,
        &$arWorkflowParameters,
        &$arWorkflowVariables,
        $arCurrentValues,
        &$errors
    ) {
        $properties = [
            'commentText' => $arCurrentValues['commentText'],
            'dealId' => $arCurrentValues['dealId'],
        ];

        $errors = self::ValidateProperties(
            $properties,
            new CBPWorkflowTemplateUser(CBPWorkflowTemplateUser::CurrentUser)
        );

        if ($errors) {
            return false;
        }

        $currentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
        $currentActivity['Properties'] = $properties;

        return true;
    }

    public function execute()
    {
        if (!CModule::IncludeModule('tasks') || !CModule::IncludeModule('forum')) {
            return CBPActivityExecutionStatus::Closed;
        }
        $this->addCommentToDeal();

        return CBPActivityExecutionStatus::Closed;
    }
}
