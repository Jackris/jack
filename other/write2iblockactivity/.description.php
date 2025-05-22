<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arActivityDescription = array(
	"NAME" => GetMessage("BPDDA_DESCR_NAME"),
	"DESCRIPTION" => GetMessage("BPDDA_DESCR_DESCR"),
	"TYPE" => "activity",
	"CLASS" => "Write2IBlockActivity",
	"JSCLASS" => "BizProcActivity",
	"CATEGORY" => array(
		"ID" => "other",
	),
	"RETURN" => array(
		
		"RecID" => array(
			"NAME" => GetMessage("BPPARCE_REC_ID"),
			"TYPE" => "int",
		),
		"LastError" => array(
	"NAME" => GetMessage("BPPARCE_LAST_ERROR"),
	"TYPE" => "string",
)
	)
);
