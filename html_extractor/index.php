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
    saveDataPage();
}

if(isset($_POST['action']) && $_POST['action'] == 'removeSuccess') {
    removeDataPage();
}

/*
if(isset($_POST['action']) && $_POST['action'] == 'updateSuccess') {
    updateDataToDatabase();
}
*/

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST)) {
    try {
        #$dataHtmlPage = addDataHtmlPageToArray();

        $wordToSearch = strtolower($_POST["searchText"]);
        $description_page = "";

        $mysqlClient = new PDO($dbname, $login, $password);

        // $sqlQuery = "SELECT * FROM word_occurence WHERE mot='$wordToSearch' ORDER BY nb_occurence DESC";
        // $result = $mysqlClient->prepare($sqlQuery);
        // $result->execute();
        // $searchWordFile = $result->fetchAll();

        $sqlQuery = "SELECT pageData.pageURL, pageData.pageTitle, pageData.pageDescription, wordList.idPage FROM page_data pageData, word_list wordList WHERE wordList.mot='$wordToSearch' AND wordList.idPage=pageData.id ORDER BY nbOccurence DESC";
        $result = $mysqlClient->prepare($sqlQuery);
        $result->execute();
        $searchWordPage = $result->fetchAll();
        print_r($searchWordPage);

        ?>
        <div class="divSearchResult">
        <?php

        #foreach ($dataHtmlPage as $url=>$pageData){
        foreach ($searchWordPage as $key=>$val){
        
            #$tabWordOccurence = getWordOccurence($pageData[0]);

            $description_page = $val["pageDescription"];
            if(strpos(strtolower($val["pageTitle"]), $wordToSearch) !== false || 
                strpos(strtolower($val["pageDescription"]), $wordToSearch) !== false || 
                    in_array($wordToSearch, $val["mot"]) !== false){
?>
            <ul>
                <a href=<?php echo $val["pageURL"]; ?> target="_blank"><i><?php echo mb_substr($val["pageURL"], 0, 40) . "..."; ?></i>
                <h3 style="font-size: 25px; margin-top:5px; margin-bottom:8px">
                    <?php 
                        echo $val["pageTitle"];
                    ?>
                </h3>
                </a>
                <?php

                
                    echo $description_page . "<br>";

                    $pageID = $val['idPage'];
                    $sqlQuery = "SELECT DISTINCT wordList.mot, wordList.nbOccurence FROM page_data pageData, word_list wordList WHERE wordList.idPage='$pageID' ORDER BY nbOccurence DESC";
                    $result = $mysqlClient->prepare($sqlQuery);
                    $result->execute();
                    $searchWordOccurence = $result->fetchAll();
                    //print_r($searchWordOccurence[0]);
                    displayArrayWordsOccurence($searchWordOccurence);
                
                    /*
                    $nbWords = 0;
                    foreach($tabWordOccurence as $val){
                        while($nbWords<10){
                            echo $val;
                            $nbWords += 1;
                        }
                        ?>

                    <?php
                    }
                    */
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