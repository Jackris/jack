<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arActivityDescription = array(
	"NAME" => GetMessage("BP_GP_TITLE"),
	"DESCRIPTION" => GetMessage("BP_GP_TITLE"),
	"TYPE" => "activity",
	"CLASS" => "GetProperty",
	"JSCLASS" => "BizProcActivity",
	"CATEGORY" => array(
		"ID" => "other",
	),
	"RETURN" => array(
		"ReturnValue" => array(
			"NAME" => GetMessage("BP_IBLOCK_RETURN_VALUE"),
			"TYPE" => "string",
		),
	),
);
?>