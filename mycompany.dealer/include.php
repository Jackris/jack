<?php

IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
    "mycompany.dealer",
    array(
        "\\Mycompany\\Dealer\\Controller\\DealerAPI" => "lib/controller/DealerAPI.php",
        "\\Mycompany\\Dealer\\ORM\\CarModelTable" => "lib/orm/CarModelTable.php",
        "\\Mycompany\\Dealer\\ORM\\DealerTable" => "lib/orm/DealerTable.php",
        "\\Mycompany\\Dealer\\ORM\\DealerToCarTable" => "lib/orm/DealerToCarTable.php",
        "\\Mycompany\\Dealer\\Agents\\CheckActivity" => "lib/agents/CheckActivity.php",
    )
);
