<?php

ini_set("allow_url_fopen ", false);
$pathParent = dirname(__FILE__);
include $pathParent . "/../credentials/credentials.php";
#require $pathParent . "/save_bdd.php";
#require $pathParent . "/html_functions.php";
require $pathParent . "/html_data_functions.php";
header('Content-Type: text/html; charset=utf-8');


$pathTextFile = $pathParent . "/../fichiers_txt";

echo removeDataDB();

?>