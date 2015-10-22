<?php

# Includes
$main_db_host = "127.0.0.1";
$main_db_user = "dbuser";
$main_db_pass = "dbpassword";
$main_db_name = "dbname";


$db_host = "127.0.0.1";
$db_user = "dbuser";
$db_pass = "dbpassword";
$db_name = "dbname";

include("functions.php");

$AdmincrudEngine = new CRUDEngine($main_db_host, $main_db_name, $main_db_user, $main_db_pass);
$MaincrudEngine = new CRUDEngine($db_host, $db_name, $db_user, $db_pass);
//$MaincrudEngine->enableTracing(1);


?>