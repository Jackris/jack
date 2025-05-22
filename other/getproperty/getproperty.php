<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;



class CBPGetProperty
	extends CBPActivity
	
{
	private $taskId = 0;
	private $isInEventActivityMode = false;

	public function __construct($name)
	{
	
global $USER;
$arGroups = $USER->GetUserGroupArray();
	
		parent::__construct($name);
		$this->arProperties = array(
			"Title" => "",
			"Users" => $arGroups,
			"IBlockID"=>null,
			"RecID"=>null,
			"PropertyName"=>"",
			"ReturnValue"=>null
			
		);
	}

	public function Execute()
	{
		
		$this->ReturnValue = null;
		if (CModule::IncludeModule('iblock'))
		{
$arrReservedWords = array("NAME", "ACTIVE_TO", "ACTIVE_FROM", "DETAIL_TEXT", "PREVIEW_TEXT", "CREATED_BY");
		$prop_name = $this->PropertyName;

if (in_array($prop_name, $arrReservedWords))
	$prop_name_sql =$prop_name_val =$prop_name;
else
{
		$prop_name_sql = "PROPERTY_".$prop_name;
		$prop_name_val = "PROPERTY_".strtoupper($prop_name)."_VALUE";
}
		$res = CIBlockElement::GetList(array("ID"=>"ASC"), array("IBLOCK_ID"=>$this->IBlockID, "ID"=>$this->RecID, "CHECK_PERMISSIONS"=>"N"), false, false, array("ID", "NAME", "IBLOCK_ID", $prop_name_sql));
			if ($ob = $res->GetNext())	
				{
					//$hd = fopen(__DIR__."/log.txt", "a");
					//fwrite($hd, "el id = ".$this->RecID." iblock id = ".$this->IBlockID. " property name = ".$prop_name_val.print_r($ob,1));

					$this->ReturnValue = $ob[$prop_name_val];
				}
		
			
		}
		
		return CBPActivityExecutionStatus::Closed;
		
	}


	
	
	public static function ValidateProperties($arTestProperties = array(), CBPWorkflowTemplateUser $user = null)
	{
		$arErrors = array();

		

		return $arErrors;
	}

	public static function GetPropertiesDialog($documentType, $activityName, $arWorkflowTemplate, $arWorkflowParameters, $arWorkflowVariables, $arCurrentValues = null, $formName = "", $popupWindow = null)
	{
		
		$runtime = CBPRuntime::GetRuntime();
		$documentService = $runtime->GetService("DocumentService");

		$arMap = array(
			
			
			"RecID"=>"requested_rec_id",
			"IBlockID"=>"requested_iblock_id",
			"PropertyName"=>'requested_property_name'
			
		);

		if (!is_array($arWorkflowParameters))
			$arWorkflowParameters = array();
		if (!is_array($arWorkflowVariables))
			$arWorkflowVariables = array();

		if (!is_array($arCurrentValues))
		{
			$arCurrentValues = array();
			$arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
			if (is_array($arCurrentActivity["Properties"]))
			{
				foreach ($arMap as $k => $v)
				{
					if (array_key_exists($k, $arCurrentActivity["Properties"]))
					{
						
							$arCurrentValues[$arMap[$k]] = $arCurrentActivity["Properties"][$k];
					}
					else
					{
						$arCurrentValues[$arMap[$k]] = "";
					}
				}
			
			}
			else
			{
				foreach ($arMap as $k => $v)
					$arCurrentValues[$arMap[$k]] = "";
			}
		}

		$arFieldTypes = $documentService->GetDocumentFieldTypes($documentType);
		$arDocumentFields = $documentService->GetDocumentFields($documentType);


		return $runtime->ExecuteResourceFile(
			__FILE__,
			"properties_dialog.php",
			array(
				"arCurrentValues" => $arCurrentValues,
				"arDocumentFields" => $arDocumentFields,
				"arFieldTypes" => $arFieldTypes,
				"javascriptFunctions" => $javascriptFunctions,
				"formName" => $formName,
				"popupWindow" => &$popupWindow,
			)
		);
		
	}

	public static function GetPropertiesDialogValues($documentType, $activityName, &$arWorkflowTemplate, &$arWorkflowParameters, &$arWorkflowVariables, $arCurrentValues, &$arErrors)
	{
		$arErrors = array();

		$runtime = CBPRuntime::GetRuntime();

		$arMap = array(
			"requested_rec_id"=>"RecID",
			"requested_iblock_id"=>"IBlockID",
			'requested_property_name'=>"PropertyName"
		);

		$arProperties = array();
		foreach ($arMap as $key => $value)
		{
			
			$arProperties[$value] = $arCurrentValues[$key];
		}
				
		$arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
		$arCurrentActivity["Properties"] = $arProperties;

		

		return true;
	}
}
?>