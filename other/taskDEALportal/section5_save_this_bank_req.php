<?php
$rootActivity = $this->GetRootActivity();
CModule::IncludeModule('crm');
$reqs_str  = '{=Variable:accounts_arr_printable}';
$reqs = unserialize ($reqs_str);

$accnum = '{=Variable:accnum_printable}';


$bank = $reqs[$accnum -1]["RQ_BANK_NAME"]. " в ".$reqs[$accnum -1]["RQ_BANK_ADDR"];
$account = $reqs[$accnum -1]["RQ_ACC_NUM"];
$corr = $reqs[$accnum -1]["RQ_COR_ACC_NUM"];
$bik = $reqs[$accnum -1]["RQ_BIK"];

$rootActivity->SetVariable("our_bank",$bank);
$rootActivity->SetVariable("our_account",$account);
$rootActivity->SetVariable("our_corr",$corr);
$rootActivity->SetVariable("our_bik",$bik);
