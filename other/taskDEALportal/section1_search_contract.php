<?php

global $USER;
$ra = $this->getRootActivity();
CModule::IncludeModule('iblock');

$deal_id = '{{ID}}';
$iblockid = '{=Constant:registry_id_printable}'; // 44

//делаем выборку из списка договоров там где id сделки = текущей
$res = CIBlockElement::GetList(
    [],
    ["IBLOCK_ID" => $iblockid, "CHECK_PERMISSIONS" => "N", "PROPERTY_SDELKA" => $deal_id],
    false,
    false,
    ["ID", "NAME", "PROPERTY_NOMER"]
);

//составляем строку из названий и номеров
$dogs = [];
$i = 0;

while ($ob = $res->GetNext()) {
    $dogs[] = $ob["NAME"] . " № " . $ob["PROPERTY_NOMER_VALUE"] . "   (#" . $ob["ID"] . ")";
    $i++;
}
$dog_str = implode("\n", $dogs);
$ra->SetVariable("doglist", $dog_str);


//{=Template:TargetUser} - пользователь, запустивший БП