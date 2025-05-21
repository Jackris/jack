<?php

$rootActivity = $this->GetRootActivity();
CModule::IncludeModule('crm');
$companies_arr = '{=Variable:companies_arr}';
$companies = unserialize($companies_arr);

$i = '{=Variable:compnum_printable}';

$fullname = $companies[$i - 1]["RQ_COMPANY_FULL_NAME"];
$shortname = $companies[$i - 1]["RQ_COMPANY_NAME"];
$kpp = $companies[$i - 1]["RQ_KPP"];
$inn = $companies[$i - 1]["RQ_INN"];

$rootActivity->SetVariable("our_name_full", $fullname);
$rootActivity->SetVariable("our_name_short", $shortname);
$rootActivity->SetVariable("our_kpp", $kpp);
$rootActivity->SetVariable("our_inn", $inn);
