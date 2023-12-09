<?php

IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
    "mcart.blago",
    array(
        "\\Mcart\\Blago\\Controller\\Help" => "lib/controller/Help.php",
        "\\Mcart\\Blago\\Controller\\BP" => "lib/controller/BP.php",
        "\\Mcart\\Blago\\ORM\\PaylistTable" => "lib/orm/PaylistTable.php",
        "\\Mcart\\Blago\\ORM\\NotifyTable" => "lib/orm/NotifyTable.php",
        "\\Mcart\\Blago\\ORM\\GroupSettingsMenuTable" => "lib/orm/GroupSettingsMenuTable.php",
        "\\Mcart\\Blago\\Events" => "lib/Events.php",
        "\\Mcart\\Blago\\Api" => "lib/Api.php",
        "\\Mcart\\Blago\\HighloadBlock" => "lib/HighloadBlock.php",
        "\\Mcart\\Blago\\HighloadBlockElement" => "lib/HighloadBlockElement.php"
    )
);
