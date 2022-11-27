<?php

ini_set("allow_url_fopen ", false);
$pathParent = dirname(__FILE__);
include $pathParent . "/credentials/credentials.php";
#require $pathParent . "/phpScript/save_bdd.php";
#require $pathParent . "/phpScript/html_functions.php";
require $pathParent . "/phpScript/html_data_functions.php";

header('Content-Type: text/html; charset=utf-8');

$pathTextFile = $pathParent . "/phpScript/fichiers_txt";

?>

<!DOCTYPE html>
<link rel="stylesheet" href="style/searchbar.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script type="text/javascript" src="script/script.js"></script>

<a href="index.php">Search Word Tool</a>

<div class="divSearchBar">
    <button onclick="addDataBDD()">Indexer les documents</button>
    <button onclick="removeDataBDD()">Supprimer les données d'indexation</button>
    <!-- <button onclick="updateDataBDD()">Vérifier données BDD</button> -->
    <div>
        <p id="operation_BDD"></p>
        <p id="output_files"></p>

    </div>
</div>

<?php
/*
if (isset($_POST['action']) && $_POST['action'] == 'addSuccess') {
    //echo "ajout des données dans la BDD";

    //saveDataDB();
}

if (isset($_POST['action']) && $_POST['action'] == 'removeSuccess') {
    removeDataDB();
}
*/
?>