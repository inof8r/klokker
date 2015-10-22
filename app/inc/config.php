<?php

# Includes
$main_db_host = "127.0.0.1";
$main_db_user = "root";
$main_db_pass = "t3mpl4r01";
$main_db_name = "busibox_db";


$db_host = "127.0.0.1";
$db_user = "root";
$db_pass = "t3mpl4r01";
$db_name = "busibox_testdb2";

include("functions.php");

$AdmincrudEngine = new CRUDEngine($main_db_host, $main_db_name, $main_db_user, $main_db_pass);
$MaincrudEngine = new CRUDEngine($db_host, $db_name, $db_user, $db_pass);
//$MaincrudEngine->enableTracing(1);


?>