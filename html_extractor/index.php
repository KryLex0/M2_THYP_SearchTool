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

//nombre d'element à afficher par page
$nbElemPage = 3;


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
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript" src="script/script.js"></script>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.6/dist/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>

<button onClick="pageAccessPassword()">Page d'insertion</button>

<!--<h3 style="text-align: center;">Outil de recherche</h3>-->
<center><a href="index.php"><img class="logo" src="ressources/logo.png" style="width:15%"></a></center>
<div class="divSearchBar">
    <form method="GET" action="index.php" id="searchWordForm" style="text-align: center;">
        <span><input id="searchTextInput" type="text" name="searchTextInput" required="required"></span>
        </br>
        <button type=submit class="btn btn-info">Rechercher</button>

    </form>

    <?php


    ?>



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
    //$varTMP = checkDataDB("../file_folder/fichier3.txt", "manger", "lemmatisation.csv", "1666090821", "");
    //print_r($varTMP);

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

            //if no page number is set, set it to 1
            if (!$_GET["numPage"]) {
                //$_POST["defaultURL"] = $_SERVER['REQUEST_URI'] . "?searchTextInput=" . $wordToSearch;
                header("LOCATION: ?searchTextInput=" . $wordToSearch . "&numPage=1");
                exit();
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

            //permet d'obtenir le nombre de page qui contiendront 6 elements de la BDD par page (58/6 arrondi au supérieur donne 10 pages de 6 élements max) 
            if (ceil(count($searchWordPage) / $nbElemPage) == 0) {
                $nbPageData = 1;
            } else {
                $nbPageData = ceil(count($searchWordPage) / $nbElemPage);
            }

            //offset qui permet d'obtenir les lignes de données à partir du numéro de page (page 2 => de l'élement 7 à ...)
            $numDepartElem = $nbElemPage * $_GET['numPage'] - $nbElemPage;
            $baseURL = strtok($_SERVER['REQUEST_URI'], "?");
            //si l'utilisateur change le numéro de page vers un nombre trop grand (sans données donc), redirige vers la dernière page contenant des données
            verifLastPage($baseURL, $nbPageData);


            $wordsCorrection = checkWordInput($wordToSearch, $lemmatisationArray);
            $nbElemArrayCorrection = count($wordsCorrection);

            $nbOccurFile = count($searchWordPage);

            if ($nbOccurFile == 0) {
                if (!empty($wordsCorrection)) {
                    if ($wordsCorrection != "" && $wordsCorrection[0] !== $wordToSearch) {
                        $i = 1;
                        echo "Vous avez peut-être voulu dire: <br><span>";
                        foreach ($wordsCorrection as $word) {
    ?>
                            <a href="<?php echo "?" . rebuilURL($word); ?>" onclick='postForm("<?php echo $word; ?>")'> <?php echo $word; ?> </a>
            <?php
                            if ($i !== $nbElemArrayCorrection) {
                                echo ", ";
                            }
                            $i += 1;
                        }
                        echo "</span>";
                    }
                }
            } else {
                $searchWordPage = array_slice($searchWordPage, $numDepartElem, $nbElemPage);
            }

            ?>
</div>

<p style="text-align: center;">Le mot '<?php echo $wordToSearch; ?>' est présent dans <?php echo $nbOccurFile;
                                                                                        if ($nbOccurFile <= 1) {
                                                                                            echo " document.";
                                                                                        } else {
                                                                                            echo " documents.";
                                                                                        } ?></p>

<?php

            #foreach ($dataHtmlPage as $url=>$pageData){
            foreach ($searchWordPage as $key => $val) {
                $description_page = $val["fileDescription"];
                $pageID = $val['id'];
                $fileURL = "html_extractore/" . $val['fileURL'];
                $nbOccurence = $val['nbOccurence'];

                if (isWordInPage($pageID)) {
?>
        <ul>
            <div class="divSearchResult">
                <a href=<?php echo $fileURL; ?> target="_blank">
                    <h3 style="font-size: 25px; margin-top:5px; margin-bottom:8px">
                        <?php
                        echo $val["fileTitle"] . " (" . $nbOccurence . ")</br>";
                        echo "<i style='font-size:15px'>$fileURL</i>";

                        ?>
                    </h3>

                </a>
                <ion-icon style="float:right; font-size:x-large" name='cloud-outline' onclick='displayWordClound(<?php echo $pageID; ?>)'></ion-icon>
                <?php


                    echo $description_page . "<br>";
                ?>
            </div><?php
                    displayWords($pageID);

                    ?>
        </ul>
        </br>

<?php
                }
            } ?>
</div>
<?php

        } catch (Exception $e) {
            // En cas d'erreur, on affiche un message et on arrête tout
            die('Erreur : ' . $e->getMessage());
        }



        if ($nbOccurFile !== 0) {

?>

<!--Bouton de numéro de page
1, 2, [3], 4, 5, ..., 30-->
<center>
<div class="changePage">
    <div class="changePageButton">

        <!-- Affiche un bouton qui redirige vers la page 1 lorsqu'on se trouve sur une autre page que la page 1 -->
        <?php if ($_GET["numPage"] != 1) { ?>
            <!-- Aller à la 1ère page -->
            <a href=<?php echo getfileURLByNumber(1); ?> class="btn btn-info" style="background-color:red"><<</a>
            <!-- Aller à la page précédente -->
            <a href=<?php echo getPreviousfileURL(); ?> class="btn btn-info" style="margin-right:10px;background-color:green"><</a>

            <a href=<?php echo getfileURLByNumber(1); ?> class="btn btn-info"><?php echo (1); ?></a>
            <?php }

            for ($i = -2; $i < 3; $i++) {
                //s'il y a une différence > 2 entre la 1ère page et la page actuelle (ou entre la dernière page et la page actuelle), affiche [...]
                if ($i == -2 && $_GET["numPage"] + $i > 2 || $i == 2 && $_GET["numPage"] + $i < $nbPageData) {
                    ?><a class="btn btn-info" readonly><?php echo ("..."); ?></a><?php
                }
                //affiche le numéro de page actuel
                elseif ($i == 0) {
                    ?><strong><a class="btn btn-info" style="background-color:grey"><?php echo ($_GET["numPage"] + $i); ?></a></strong><?php
                    //affiche les numéros de pages précédents et suivants autour de la page actuelle
                } elseif ($_GET["numPage"] + $i > 1 && $_GET["numPage"] + $i < $nbPageData) {
                    ?>
                    <a href=<?php echo getfileURLByNumber($_GET["numPage"] + $i); ?> class="btn btn-info"><?php echo ($_GET["numPage"] + $i); ?></a>
                <?php
                }
            }
                //Affiche un bouton qui redirige vers la dernière page lorsqu'on se trouve sur une autre page que la dernière
                if ($_GET["numPage"] != $nbPageData) { ?>
                    <a href=<?php echo getfileURLByNumber($nbPageData); ?> class="btn btn-info"><?php echo ($nbPageData); ?></a>
                    <!-- Aller à la page suivante -->
                    <a href=<?php echo getNextfileURL(); ?> class="btn btn-info" style="margin-left:10px;background-color:green">></a>
                    <!-- Aller à la dernière page -->
                    <a href=<?php echo getfileURLByNumber($nbPageData); ?> class="btn btn-info" style="background-color:red">>></a>
            <?php } ?>


    </div>
</div>
                </center>
<?php
        }
    }

?>