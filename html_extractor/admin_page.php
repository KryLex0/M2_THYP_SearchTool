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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.6/dist/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>

<a href="index.php">Search Word Tool</a>

<div class="divSearchBar">
    <button onclick="addDataBDD()">Indexer les documents</button>
    <button onclick="removeDataBDD()">Supprimer les données d'indexation</button>
    <!-- <button onclick="updateDataBDD()">Vérifier données BDD</button> -->
    <div>
        <p id="operation_BDD"></p>
        <div class="spinner-border text-primary loading" id="loading" style="display:none;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
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