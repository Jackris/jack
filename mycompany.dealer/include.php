<?php

IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
    "mycompany.dealer",
    array(
        "\\Mycompany\\Dealer\\Controller\\Main" => "lib/controller/Main.php",
        "\\Mycompany\\Dealer\\ORM\\CarModelTable" => "lib/orm/CarModelTable.php",
        "\\Mycompany\\Dealer\\ORM\\DealerTable" => "lib/orm/DealerTable.php",
        "\\Mycompany\\Dealer\\ORM\\DealerToCarTable" => "lib/orm/DealerToCarTable.php",
        "\\Mycompany\\Dealer\\Events" => "lib/Events.php",
    )
);
