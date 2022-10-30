<?php
ini_set("allow_url_fopen ", false);
$pathParent = dirname(__FILE__);
include $pathParent . "/credentials/credentials.php";
require $pathParent . "/save_bdd.php";
require $pathParent . "/html_functions.php";

header('Content-Type: text/html; charset=utf-8');

$pathTextFile = $pathParent . "/fichiers_txt";


//$htmlLinksArray = addHtmlLinksToArray();

/*
$htmlUrl = "https://fr.wikipedia.org/wiki/Liste_des_%C3%A9pisodes_de_One_Piece";//"https://stackoverflow.com/questions/819182/how-do-i-get-the-html-code-of-a-web-page-in-php";"https://www.youtube.com/watch?v=vvN8jr-CGiE"
$htmlData = get_html_data($htmlUrl);

$titlePage = getTitle($htmlUrl);
$bodyPage = getBody($htmlUrl);

echo "Title: " . $titlePage . " |||||||||||| \n";
echo "Body: " . $bodyPage . "//////////// \n";

echo "MetaData: \n";
$metadatas = getMetaData($htmlUrl);
foreach($metadatas as $meta=>$val){
    echo $meta . "====>" . $val . " |||||||||||| ";
}

*/


?>
<!DOCTYPE html>
<script src="searchbar.js" type="text/javascript"></script>
<link rel="stylesheet" href="style/searchbar.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<button onclick="addDataBDD()">Ajouter données BDD</button>
<button onclick="removeDataBDD()">Supprimer données BDD</button>
<!-- <button onclick="updateDataBDD()">Vérifier données BDD</button> -->

<h3 style="text-align: center;">Outil de recherche</h3>
<div class="divSearchBar">
    <form method="POST" action="index.php" id="rechercheMot" style="text-align: center;">
        <span><input type="text" name="searchText" required="required">
        <input type=submit value="Rechercher" name="searchButton">
    </form>
</div>


<?php


$tab_fichiers = addFileNameToArray(); // array (nomFichier => timestamp) de tout les fichiers

$all_tab = addFileWordOccurence($tab_fichiers); //array (nomFichier => array(mots=>nbOccurence))
//updateDataToDatabase();

if(isset($_POST['action']) && $_POST['action'] == 'addSuccess') {
    addDataToDatabase($tab_fichiers, $all_tab);
}
if(isset($_POST['action']) && $_POST['action'] == 'removeSuccess') {
    removeDataToDatabase();
}
/*
if(isset($_POST['action']) && $_POST['action'] == 'updateSuccess') {
    updateDataToDatabase();
}
*/

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST)) {
    try {
        $dataHtmlPage = addDataHtmlPageToArray();

        $wordToSearch = strtolower($_POST["searchText"]);
        $description_page = "";

        ?>
        <div class="divSearchResult">
        <?php

        foreach ($dataHtmlPage as $url=>$pageData){
            if(array_key_exists("description", $pageData[0]["MetaData"])){
                $description_page = implode(' ', array_slice(explode(' ', $pageData[0]["MetaData"]["description"]), 0, 13)) . "</br>" . implode(' ', array_slice(explode(' ', $pageData[0]["MetaData"]["description"]), 13, 15)) . "...";
                //echo $pageData[0]["MetaData"]["description"];
            }else{
                $description_page = implode(' ', array_slice(explode(' ', $pageData[0]["BodyContent"]), 0, 13)) . "</br>" . implode(' ', array_slice(explode(' ', $pageData[0]["BodyContent"]), 13, 15)) . "...";
                //echo mb_substr($bodyPage, 0, 80) . "</br>" . mb_substr($bodyPage, 80, 80) . "...";
            }
            if(strpos(strtolower($pageData[0]["Title"]), $wordToSearch) !== false || 
                strpos(strtolower($description_page), $wordToSearch) !== false || 
                    strpos(strtolower($pageData[0]["BodyContent"]), $wordToSearch) !== false){
?>
            <ul>
                <a href=<?php echo $url; ?> target="_blank"><i><?php echo mb_substr($url, 0, 40) . "..."; ?></i>
                <h3 style="font-size: 25px; margin-top:5px; margin-bottom:8px">
                    <?php 
                        echo $pageData[0]["Title"];
                    ?>
                </h3>
                </a>
                <?php
                    echo $description_page;
                ?>
            </ul>
            </br>

<?php 
            }
        } ?>
        </div>
<?php
        unset($_POST);
        unset($_REQUEST);
    } catch (Exception $e) {
        // En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : ' . $e->getMessage());
    }
}

?>