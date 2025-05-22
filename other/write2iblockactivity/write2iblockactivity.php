<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule('iblock');
//71008
class CBPWrite2IBlockActivity extends CBPActivity
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->arProperties = array(
            "Title" => "",
            "MapFields" => null,
            "rec_id" => null,
            "iblock_id" => null,
            "RecID" => null,
            "LastError" => null,
        );
    }

    public static function CorrectToBeEmpty($mainArray)
    {
        foreach ($mainArray as $key => $val) {
            if ($key == "PROPERTY_VALUES") {
                continue;
            }
            if (empty($val)) {
                unset($mainArray[$key]);
            }
        }

        return $mainArray;
    }

    public function Execute()
    {
        global $USER, $APPLICATION;

        if (is_array($this->__get("MapFields"))) {
            $printVal = $this->__get("MapFields");
            $arrProp = array();
            $arrPropType = array();
            $IBLOCK_ID = $this->iblock_id;
            $REC_ID = $this->rec_id;
            $NAME = "tmp" . date("d-m-y");
            $resPropType = CIBlock::GetProperties($IBLOCK_ID);
            while ($res_prop_type_arr = $resPropType->Fetch()) {
                $arrPropType[$res_prop_type_arr["CODE"]] = array(
                    "PROPERTY_TYPE" => $res_prop_type_arr["PROPERTY_TYPE"],
                    "USER_TYPE" => $res_prop_type_arr["USER_TYPE"],
                    "LINK_IBLOCK_ID" => $res_prop_type_arr["LINK_IBLOCK_ID"],
                    "MULTIPLE" => $res_prop_type_arr["MULTIPLE"]
                );
            }

            foreach ($printVal as $key => $val) {
                $str = $arrPropType[$key];

                if ($key == "NAME") {
                    $NAME = $val;
                } elseif ($key == "PREVIEW_TEXT") {
                    $PREVIEW_TEXT = $val;
                } elseif ($key == "DETAIL_TEXT") {
                    $DETAIL_TEXT = $val;
                } elseif ($key == "DATE_ACTIVE_FROM") {
                    $date = str_replace('&nbsp;', ' ', $val);
                    $DATE_ACTIVE_FROM = FormatDateFromDB($date, "FULL");
                } elseif ($key == "DATE_ACTIVE_TO") {
                    $date = str_replace('&nbsp;', ' ', $val);
                    $DATE_ACTIVE_TO = FormatDateFromDB($date, "FULL");
                } else {
                    if ((is_array($val)) && ($str["PROPERTY_TYPE"] != "F")) {
                        $htmlvalue = array();
                        foreach ($val as $k => $v) {
                            if (is_array($v)) {
                                $htmlvalue[$k] = array();
                                foreach ($v as $k1 => $v1)
                                    $htmlvalue[$k][$k1] = htmlspecialcharsbx($v1);
                            } else {
                                $htmlvalue[$k] = htmlspecialcharsbx($v);
                            }
                        }
                        $val1 = $htmlvalue;
                    } else {
                        $val1 = "";
                        if (($str['USER_TYPE'] == 'employee') || ($str['USER_TYPE'] == 'UserID')) {
                            if (substr($val, 0, 5) == 'user_') {
                                $USER_ID = substr($val, 5);
                            } else {
                                $USER_ID = $val;
                            }

                            $val1 = $USER_ID;
                        } elseif ($str['USER_TYPE'] == 'DateTime') {
                            $date = str_replace('&nbsp;', ' ', $val);
                            $val1 = FormatDateFromDB($date, "FULL");
                        } elseif (($str["PROPERTY_TYPE"] == "S") && ($str['USER_TYPE'] == 'HTML')) {
                            $val1 = array("VALUE" => array("TEXT" => txttohtml($val), "TYPE" => "html"));
                        } elseif ($str["PROPERTY_TYPE"] == "F") {
                            $val1 = [];

                            $pattern = '/\&\i=([0-9]+)\&/';

                            preg_match_all($pattern, $val, $arrFID);
                            $fid = array();
                            if (is_array($arrFID)) {
                                if (!empty($arrFID[1])) {
                                    foreach ($arrFID[1] as $fid_) {
                                        $fid[] = CFile::CopyFile($fid_);
                                    }
                                } else {
                                    $fid[] = CFile::CopyFile($val);
                                }
                            } else {
                                $fid[] = CFile::CopyFile($val);
                            }
                            if ($str["MULTIPLE"] == "N") {
                                foreach ($fid as $fileID) {
                                    $val1 = $fileID;
                                }
                            } else {
                                if (!is_array($val1)) {
                                    $val1 = array();
                                }
                                foreach ($fid as $fileID) {
                                    $val1[] = $fileID;
                                }
                            }
                        } else {
                            $val1 = $val;
                        }
                    }

                    $arrProp[$key] = $val1;
                }
            }

            $el = new CIBlockElement;
            if (empty($REC_ID)) {
                $arLoadProductArray = array(
                    "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                    "IBLOCK_ID"      => $IBLOCK_ID,
                    "DATE_ACTIVE_FROM" => $DATE_ACTIVE_FROM,
                    "DATE_ACTIVE_TO" => $DATE_ACTIVE_TO,
                    "PROPERTY_VALUES" => $arrProp,
                    "NAME"           => $NAME,
                    "PREVIEW_TEXT" => $PREVIEW_TEXT,
                    "DETAIL_TEXT" => $DETAIL_TEXT,
                    "ACTIVE"         => "Y",            // активен

                );
                $arLoadProductArray = self::CorrectToBeEmpty($arLoadProductArray);
                $PRODUCT_ID = $el->Add($arLoadProductArray, true);
                if (intval($PRODUCT_ID) > 0) {
                    $this->RecID = $PRODUCT_ID;
                } else {
                    $this->LastError = $el->LAST_ERROR;
                }
            } else {
                $arLoadProductArray = array(
                    "DATE_ACTIVE_FROM" => $DATE_ACTIVE_FROM,
                    "DATE_ACTIVE_TO" => $DATE_ACTIVE_TO,
                    "NAME"           => $NAME,
                    "PREVIEW_TEXT" => $PREVIEW_TEXT,
                    "DETAIL_TEXT" => $DETAIL_TEXT,
                    "ACTIVE"         => "Y",
                );

                unset($arLoadProductArray["PROPERTY_VALUES"]);
                $arLoadProductArray = self::CorrectToBeEmpty($arLoadProductArray);

                $el->Update($REC_ID, $arLoadProductArray, true);
                $this->LastError = $el->LAST_ERROR;

                foreach ($arrProp as $pr_key => $pr_val) {
                    CIBlockElement::SetPropertyValueCode($REC_ID, $pr_key, $pr_val);
                }
                $this->RecID = $REC_ID;
            }
        }

        return CBPActivityExecutionStatus::Closed;
    }

    public static function GetPropertiesDialog($documentType, $activityName, $arWorkflowTemplate, $arWorkflowParameters, $arWorkflowVariables, $arCurrentValues = null, $formName = "")
    {
        $runtime = CBPRuntime::GetRuntime();

        if (!is_array($arCurrentValues)) {
            $arCurrentValues = array();

            $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);

            if (
                is_array($arCurrentActivity["Properties"])
                && array_key_exists("MapFields", $arCurrentActivity["Properties"])
                && is_array($arCurrentActivity["Properties"]["MapFields"])
            ) {
                foreach ($arCurrentActivity["Properties"]["MapFields"] as $k => $v) {
                    $arCurrentValues["MapFields"][$k] = $v;
                }

                $arCurrentValues["iblock_id"] = $arCurrentActivity["Properties"]["iblock_id"];
                $arCurrentValues["rec_id"] = $arCurrentActivity["Properties"]["rec_id"];
            }
        }

        $runtime = CBPRuntime::GetRuntime();
        return $runtime->ExecuteResourceFile(
            __FILE__,
            "properties_dialog.php",
            array(
                "arCurrentValues" => $arCurrentValues,
                "formName" => $formName,
            )
        );
    }

    public static function GetPropertiesDialogValues($documentType, $activityName, &$arWorkflowTemplate, &$arWorkflowParameters, &$arWorkflowVariables, $arCurrentValues, &$arErrors)
    {
        $runtime = CBPRuntime::GetRuntime();
        $arProperties = array("MapFields" => array());

        if (is_array($arCurrentValues) && count($arCurrentValues) > 0) {
            if (
                is_array($arCurrentValues["fields"]) && count($arCurrentValues["fields"]) > 0
                && is_array($arCurrentValues["values"]) && count($arCurrentValues["values"]) > 0
            ) {
                foreach ($arCurrentValues["fields"] as $key => $value)
                    if (strlen($value) > 0 && strlen($arCurrentValues["values"][$key]) > 0)
                        $arProperties["MapFields"][$value] = $arCurrentValues["values"][$key];
            }

            $arProperties["iblock_id"] = $arCurrentValues["iblock_id"];
            $arProperties["rec_id"] = $arCurrentValues["rec_id"];
        }

        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
        $arCurrentActivity["Properties"] = $arProperties;

        return true;
    }

    public static function ValidateProperties($arTestProperties = array(), CBPWorkflowTemplateUser $user = null)
    {
        $arErrors = array();

        return array_merge($arErrors, parent::ValidateProperties($arTestProperties, $user));
    }
}
