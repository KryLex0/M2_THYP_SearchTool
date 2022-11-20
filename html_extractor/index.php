<?php
ini_set("allow_url_fopen ", false);
$pathParent = dirname(__FILE__);
include $pathParent . "/credentials/credentials.php";
#require $pathParent . "/save_bdd.php";
#require $pathParent . "/html_functions.php";
require $pathParent . "/html_data_functions.php";

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
<link rel="stylesheet" href="style/searchbar.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script type="text/javascript" src="script/script.js"></script>

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


#$tab_fichiers = addFileNameToArray(); // array (nomFichier => timestamp) de tout les fichiers

#$all_tab = addFileWordOccurence($tab_fichiers); //array (nomFichier => array(mots=>nbOccurence))
//updateDataToDatabase();

if (isset($_POST['action']) && $_POST['action'] == 'addSuccess') {
    saveDataDB();
}

if (isset($_POST['action']) && $_POST['action'] == 'removeSuccess') {
    removeDataDB();
}

/*
if(isset($_POST['action']) && $_POST['action'] == 'updateSuccess') {
    updateDataToDatabase();
}
*/

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST)) {
    try {
        //saveDataPage();
        #$dataHtmlPage = addDataHtmlPageToArray();

        $wordToSearch = mb_strtolower($_POST["searchText"]);
        $description_page = "";

        

        // $sqlQuery = "SELECT * FROM word_occurence WHERE mot='$wordToSearch' ORDER BY nb_occurence DESC";
        // $result = $mysqlClient->prepare($sqlQuery);
        // $result->execute();
        // $searchWordFile = $result->fetchAll();

        $searchWordPage = getAllPageData($wordToSearch);
?>
        <div class="divSearchResult">

            <p style="text-align: center;">Le mot '<?php echo $wordToSearch; ?>' est présent dans <?php echo count($searchWordPage);
                                                                                                    if (count($searchWordPage) <= 1) {
                                                                                                        echo " fichier.";
                                                                                                    } else {
                                                                                                        echo " fichiers.";
                                                                                                    } ?></p>

            <?php

            #foreach ($dataHtmlPage as $url=>$pageData){
            foreach ($searchWordPage as $key => $val) {
                $description_page = $val["pageDescription"];
                $pageID = $val['id'];
                $pageUrl = $val['pageURL'];

                if (isWordInPage($pageID)) {
            ?>
                    <ul>
                        <a href=<?php echo $pageUrl; ?> target="_blank"><i><?php echo mb_substr($pageUrl, 0, 40) ; ?></i>
                            <h3 style="font-size: 25px; margin-top:5px; margin-bottom:8px">
                                <?php
                                echo $val["pageTitle"];
                                ?>
                            </h3>
                        </a>
                        <ion-icon style="float:right; font-size:x-large" name='cloud-outline' onclick='displayWordClound(<?php echo $pageID;?>)'></ion-icon>
                        <?php


                        echo $description_page . "<br>";

                        displayWords($pageID);

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