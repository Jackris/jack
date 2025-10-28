<?php

use Bitrix\Bizproc\Activity\PropertiesDialog;
use Bitrix\Disk\Driver;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use Mcart\SocialNetwork\Workgroups;
use Bitrix\Tasks\Integration\Forum\Task\Comment;
use Bitrix\Forum\MessageTable;
use Bitrix\Main\Entity\ReferenceField;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class CBPMcartGetTaskResultString extends CBPActivity
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->arProperties = [
            'taskId' => null,
            'resultString' => '',
        ];
    }

    public function getTaskResultCommentFiles(): void
    {
        $commentForumId = Comment::getForumId();
        $taskId = (int)$this->taskId;

        if (empty($commentForumId)) {
            throw new SystemException('Ошибка! Не найден ID форума комментариев.');
        }

        if (empty($taskId)) {
            throw new SystemException('Ошибка! Некорректный ID задачи');
        }

        $resultComment = MessageTable::getList([
            'select' => ['POST_MESSAGE','ID'],
            'filter' => [
                '=SERVICE_DATA' => 'TASK_RESULT',
                'REAL_TOPIC.FORUM_ID' => $commentForumId,
                'REAL_TOPIC.XML_ID' => 'TASK_' . $taskId,
                '!=PARAM1' => 'TK',
            ],
            'order' => ['POST_DATE' => 'DESC'],
            'runtime' => [
                new ReferenceField(
                    'REAL_TOPIC',
                    '\Bitrix\Forum\TopicTable',
                    ['=this.TOPIC_ID' => 'ref.ID']
                ),

            ],
            'limit' => 1,
        ]);

        if (!$resultComment)
        {
            throw new SystemException('Ошибка! Не найден комментарий с результами задачи');
        }
        $resultComment = $resultComment->fetch();
        $resultCommentString = $resultComment['POST_MESSAGE'];

        if ($resultCommentString){
            $this->resultString = $resultCommentString;
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
            'taskId' => [
                'Name' => GetMessage('MCART_GTRF_TASK_ID'),
                'FieldName' => 'task_id',
                'Type' => 'string',
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
            'taskId' => $arCurrentValues['task_id'],
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

        try {
            $this->getTaskResultCommentFiles();
        } catch (Throwable $error) {
            $this->WriteToTrackingService($error);
        }

        return CBPActivityExecutionStatus::Closed;
    }
}
