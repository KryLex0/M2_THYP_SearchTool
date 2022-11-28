<?php
ini_set("allow_url_fopen ", false);
$pathParent = dirname(__FILE__);
include $pathParent . "/credentials/credentials.php";
#require $pathParent . "/save_bdd.php";
#require $pathParent . "/html_functions.php";
require $pathParent . "/phpScript/html_data_functions.php";

header('Content-Type: text/html; charset=UTF-8');
//header('Content-Type: text/html; charset=UTF-8');

$pathTextFile = $pathParent . "/phpScript/fichiers_txt";


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

<button onClick="pageAccessPassword()">Page d'insertion</button>

<!--<h3 style="text-align: center;">Outil de recherche</h3>-->
<center><a href="index.php"><img class="logo" src="ressources/logo.png" style="width:15%"></a></center>
<div class="divSearchBar">
    <form method="POST" action="index.php" id="searchWordForm" style="text-align: center;">
        <span><input id="searchTextInput" type="text" name="searchTextInput" required="required">
            <input type=submit value="Rechercher" name="searchButton">
    </form>



    <?php


    #$tab_fichiers = addFileNameToArray(); // array (nomFichier => timestamp) de tout les fichiers

    #$all_tab = addFileWordOccurence($tab_fichiers); //array (nomFichier => array(mots=>nbOccurence))
    //updateDataToDatabase();



    /*
if(isset($_POST['action']) && $_POST['action'] == 'updateSuccess') {
    updateDataToDatabase();
}
*/
    //print_r(fgetcsv("lemmatisation.csv", ";"));

    /*
if(in_array("manger", $lemmatisation)){
    echo "manger est dans le tableau";
}
else{
    echo "manger n'est pas dans le tableau";
}*/

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST) || isset($_GET["searchTextInput"])) {
        try {
            //saveDataPage();
            #$dataHtmlPage = addDataHtmlPageToArray();
            $wordToSearch = "";
            if (isset($_GET["searchTextInput"])) {
                $wordToSearch = $_GET["searchTextInput"];
            } else if (isset($_POST["searchTextInput"])) {
                $wordToSearch = $_POST["searchTextInput"];
            }
            //$wordToSearch = mb_strtolower($_POST["searchTextInput"]);
            $description_page = "";
            $lemmatisationArray = getWordsLemmatisation();
            $wordToSearch = checkLemmatisationWord($wordToSearch, $lemmatisationArray);


            // $sqlQuery = "SELECT * FROM word_occurence WHERE mot='$wordToSearch' ORDER BY nb_occurence DESC";
            // $result = $mysqlClient->prepare($sqlQuery);
            // $result->execute();
            // $searchWordFile = $result->fetchAll();

            $searchWordPage = getAllPageData($wordToSearch);

            $wordsCorrection = checkWordInput($wordToSearch, $lemmatisationArray);
            $nbElemArrayCorrection = count($wordsCorrection);

            if (count($searchWordPage) == 0) {
                if ($wordsCorrection != "") {
                    $i = 1;
                    echo "Vous avez peut-être voulu dire: <br><span>";
                    foreach ($wordsCorrection as $word) {
    ?>
                        <a href="#" onclick='postForm("<?php echo $word; ?>")'> <?php echo $word; ?> </a>
            <?php
                        if ($i !== $nbElemArrayCorrection) {
                            echo ", ";
                        }
                        $i += 1;
                    }
                    echo "</span>";
                }
            }

            ?>
</div>
<div class="divSearchResult">

    <p style="text-align: center;">Le mot '<?php echo $wordToSearch; ?>' est présent dans <?php echo count($searchWordPage);
                                                                                            if (count($searchWordPage) <= 1) {
                                                                                                echo " document.";
                                                                                            } else {
                                                                                                echo " documents.";
                                                                                            } ?></p>

    <?php

            #foreach ($dataHtmlPage as $url=>$pageData){
            foreach ($searchWordPage as $key => $val) {
                $description_page = $val["pageDescription"];
                $pageID = $val['id'];
                $pageUrl = "html_extractore/" . $val['pageURL'];

                if (isWordInPage($pageID)) {
    ?>
            <ul>
                <a href=<?php echo $pageUrl; ?> target="_blank"><i><?php echo $pageUrl; ?></i>
                    <h3 style="font-size: 25px; margin-top:5px; margin-bottom:8px">
                        <?php
                        echo $val["pageTitle"];
                        ?>
                    </h3>
                </a>
                <ion-icon style="float:right; font-size:x-large" name='cloud-outline' onclick='displayWordClound(<?php echo $pageID; ?>)'></ion-icon>
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
            unset($_GET);
            unset($_POST);
            unset($_REQUEST);
        } catch (Exception $e) {
            // En cas d'erreur, on affiche un message et on arrête tout
            die('Erreur : ' . $e->getMessage());
        }
    }

?>