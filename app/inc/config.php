<?php

# Includes
include($_SERVER["DOCUMENT_ROOT"] . "/bb_config.php");

include("functions.php");

$AdmincrudEngine = new CRUDEngine($main_db_host, $main_db_name, $main_db_user, $main_db_pass);
$MaincrudEngine = new CRUDEngine($db_host, $db_name, $db_user, $db_pass);
//$MaincrudEngine->enableTracing(1);


?>