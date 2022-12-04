<?php

$pathParent = dirname(__FILE__);

include $pathParent . "/../credentials/credentials.php";
include $pathParent . "/explore_dir_recursiv.php";
include $pathParent . "/../pdfClass/class.pdf2text.php";
header('Content-Type: text/html; charset=UTF-8');
$path = $pathParent . "/../fichiers_txt";
$pathLemmatisationFile = $pathParent . "/../ressources/lemmatisation.csv";

$mysqlClient = $mysqlClient;

//type de fichier prit en charge
$extensionAccepte = array("txt", "html", "pdf");

//lancement de la fonction qui explore tout les dossiers dans le répertoire "docs"
//explorerDir($path, $extensionAccepte);
$arrayPathFile = array();

$fileFolder = "../file_folder";

function getFileContent($path){
    $fileContent = file_get_contents($path);
    return $fileContent;
}

// return array of possible words based on user input
function checkWordInput($word, $lemmatisationArray){
    $arrayCorrection = array();
    foreach ($lemmatisationArray as $arrayLemm) {
        if(!empty($arrayLemm[1])){
            //check if the word is in the array
            if(startsWith($arrayLemm[1], $word)){
                //add the base word to the array
                array_push($arrayCorrection, $arrayLemm[1]);
            }
        }
    }
    //return the array of possible words without duplicates
    return array_unique($arrayCorrection);
}

// check if a string starts with a specific string
function startsWith ($string, $startString){
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}

// return array of words from the lemmatisation file
function getWordsLemmatisation(){
    $arrayLemmatisation = array();

    if (($handle = fopen($GLOBALS["pathLemmatisationFile"], "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 50,";")) !== FALSE) {
            array_push($arrayLemmatisation, $data);
        }
        fclose($handle);
    }
    return $arrayLemmatisation;
}

// check if the word is in the lemmatisation file
// if it is, return the base word
function checkLemmatisationWord($word, $lemmatisationArray){
    $word = mb_strtolower($word, 'UTF-8');

    foreach ($lemmatisationArray as $arrayLemm) {
    //print_r($arrayLemm);
    //if(in_array("manger", $arrayLemm)){
        if($word === $arrayLemm[0]){
            if(empty($arrayLemm[1])){
                //echo $arrayLemm[0];
                $word = mb_strtolower($arrayLemm[0], 'UTF-8');
            }else{
                //echo $arrayLemm[1];
                $word = mb_strtolower($arrayLemm[1], 'UTF-8');

            }
            
        }
    }
    return $word;
}

//function that returns the title of the html data page 
function getTitlePage($url) {
    $data = file_get_contents($url);
    $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $data, $matches) ? $matches[1] : null;
    return str_replace("'", "\'", $title);
}

//function that returns the title of the html data page 
function getMetaDataPage($url) {
    $metaTags = get_meta_tags($url);
    return $metaTags;
}
/*
// function to get <p> in body
// /!\ but not working great
function getBodyContentPage($url){
    $data = file_get_contents($url);
    $bodyContent = strval(strip_tags($data, '<p>' . '<a>' . '<b>' . '<i>' . '<u>' . '<br>' . '<br/>' . '<br />' . '<h1>' . '<h2>' . '<h3>' . '<h4>' . '<h5>' . '<h6>'));

    return $bodyContent;
}
*/

// function to get all <p> in body
function getBodyContentPage($url){
    $data = file_get_contents($url);
    $bodyContent = "";

    $dom = new DOMDocument;
    @$dom->loadHTML($data);
    foreach ($dom->getElementsByTagName('p') as $tag) {
        $bodyContent = $bodyContent . $tag->nodeValue; // concat all paragraphs together and return it
    }
    
    return str_replace("'", "\'", $bodyContent);
}

function getDescriptionPage($metaData, $bodyContent){
    $description_page = "";
    if (array_key_exists("description", $metaData)) {
        $descriptionTmp = $metaData["description"];
    } else {
        $descriptionTmp = $bodyContent;
    }

    $description_page = implode(' ', array_slice(explode(' ', $descriptionTmp), 0, 13));
    if(strlen($descriptionTmp) > 13){
        $description_page .= "</br>" . implode(' ', array_slice(explode(' ', $descriptionTmp), 13, 15));
        if(strlen($descriptionTmp) > 28){
            $description_page .= "...";
        }
    }

    return str_replace("'", "\'", $description_page);
}



function getArrayFromPageData($titlePage, $bodyContentPage, $metaDataPage){
    $separateurs =  "'`’\". -+=*!?;/\n\t\r,…][(«»<>)";
    $arrayWordOccurence = array();

    $arrayWordOccurence = getWordOccurenceTitle($arrayWordOccurence, $titlePage);
    $arrayWordOccurence = getWordOccurenceMetaData($arrayWordOccurence, $metaDataPage);
    $arrayWordOccurence = getWordOccurenceBody($arrayWordOccurence, $bodyContentPage);


    return $arrayWordOccurence;
    
}

//lecture du fichier de mots vide et ajout des mots vide dans une array
function addEmptyWordToArray(){
    $tab_mots_vide = array();
    $fichier_mots_vide = strtolower(file_get_contents($GLOBALS["pathParent"] . '/../ressources/mots_vide/fichier_mots_vide.txt'));

    $separateurs =  "'`’\". -+=*!?;/\n\t\r,…][(«»<>)";
    $tok =  strtok($fichier_mots_vide, $separateurs);

    while ($tok !== false) {
        //echo $tok . " || ";
        array_push($tab_mots_vide, $tok);
        $tok = strtok($separateurs);
    }
    return $tab_mots_vide;
}

function getWordOccurenceTitle($arrayWordOccurence, $titlePage){
    $arrayEmptyWords = addEmptyWordToArray();
    $separateurs =  "'`’\". -+=*!?;/\n\t\r,…][(«»<>)";
    $tok =  strtok($titlePage, $separateurs);
    $lemmatisationArray = getWordsLemmatisation();

    while ($tok !== false) {
        $word = checkLemmatisationWord($tok, $lemmatisationArray);
        if ((strlen($word) > 2) && !in_array($word, $arrayEmptyWords)) {
            if (array_key_exists($word, $arrayWordOccurence)) {
                $arrayWordOccurence[$word] += 2;
            } else {
                $arrayWordOccurence[$word] = 2;
            }
        }
        $tok = strtok($separateurs);
    }
    arsort($arrayWordOccurence);
    

    return $arrayWordOccurence;
}

function getWordOccurenceMetaData($arrayWordOccurence, $metaDataPage){
    $arrayEmptyWords = addEmptyWordToArray();
    $separateurs =  "'`’\". -+=*!?;/\n\t\r,…][(«»<>)";
    $lemmatisationArray = getWordsLemmatisation();

    foreach ($metaDataPage as $metaDataKey => $metaDataValue){
        $tok =  strtok($metaDataValue, $separateurs);

        while ($tok !== false) {
            $word = checkLemmatisationWord($tok, $lemmatisationArray);
            if ((strlen($tok) > 2) && !in_array($word, $arrayEmptyWords)) {
                if (array_key_exists($word, $arrayWordOccurence)) {
                    $arrayWordOccurence[$word] += 1;
                } else {
                    $arrayWordOccurence[$word] = 1;
                }
            }
            $tok = strtok($separateurs);
        }
        arsort($arrayWordOccurence);
    }
    return $arrayWordOccurence;
}

function getWordOccurenceBody($arrayWordOccurence, $bodyContentPage){
    $arrayEmptyWords = addEmptyWordToArray();
    $separateurs =  "'`’\". -+=*!?;/\n\t\r,…][(«»<>)";
    $tok =  strtok($bodyContentPage, $separateurs);
    $lemmatisationArray = getWordsLemmatisation();

    while ($tok !== false) {
        $word = checkLemmatisationWord($tok, $lemmatisationArray);

        if ((strlen($word) > 2) && !in_array($word, $arrayEmptyWords)) {
            if (array_key_exists($word, $arrayWordOccurence)) {
                $arrayWordOccurence[$word] += 1;
            } else {
                $arrayWordOccurence[$word] = 1;
            }
        }
        $tok = strtok($separateurs);
    }
    arsort($arrayWordOccurence);
    

    return $arrayWordOccurence;
}




/***************************************************************************/

function saveDataDB(){
    $arrayFiles = explorerDir($GLOBALS["fileFolder"], $GLOBALS["extensionAccepte"], $GLOBALS["arrayPathFile"]);    
    foreach ($arrayFiles as $file){
        //if its a html file
        $fileContent = getFileContent($file);
        $timestampFile = filemtime($file);

        if(strpos($file, ".html") || strpos($file, ".HTML")){
            #echo $fileContent;
            $titlePage = getTitlePage($file);
            $metaDataPage = getMetaDataPage($file);
            $bodyContentPage = getBodyContentPage($file);
            $descriptionPage = getDescriptionPage($metaDataPage, $bodyContentPage);
            #echo $title . "::" . $bodyContent . "</br>";
            #print_r($metaData);
            $arrayPageData = getArrayFromPageData($titlePage, $bodyContentPage, $metaDataPage);
            checkDataDB($file, $titlePage, $descriptionPage, $timestampFile, $arrayPageData);
            //echo $titlePage . " || " . $descriptionPage . "</br>";
        }
        else if(strpos($file, ".txt") || strpos($file, ".TXT")){
            $arrayTextDataFile = getArrayFromTextFile($file, "");
            $titleFile = implode(' ', array_slice(explode(' ', $fileContent), 0, 5));
//"Fichier Texte: " . $file;
            $descriptionFile = getDescriptionTextFile($fileContent);
            checkDataDB($file, $titleFile, $descriptionFile, $timestampFile, $arrayTextDataFile);
            //echo $titleFile . " || " . $descriptionFile . "</br>";
        } else if(strpos($file, ".pdf") || strpos($file, ".PDF")){
            $az = new PDF2Text();
            $az->setFilename($file); 
            $az->decodePDF();
            $textPDFContent = utf8_encode($az->output());
            
            $arrayTextData = getArrayFromTextFile("", $textPDFContent);
            $titleFile = "Fichier PDF: " . $file;
            $descriptionFile = getDescriptionTextFile($textPDFContent);
            checkDataDB($file, $titleFile, $descriptionFile, $timestampFile, $arrayTextData);



        }
        echo "Fichier traité: " . $file . "</br>";
        //echo '<script type="text/javascript">document.getElementByClassName("output_files").innerHTML = 5 + 6;</script>';

    }
    //return $arrayFiles;
}

function getArrayFromTextFile($filePath, $fileContentText){
    $arrayWordOccurence = array();
    $arrayEmptyWords = addEmptyWordToArray();
    $fileContent = "";
    if(empty($filePath)){
        $fileContent = $fileContentText;
    }else {
        $fileContent = file_get_contents($filePath);
    }
    
    //$fileContent = file_get_contents($filePath);
    $separateurs =  "'`’\". -+=*!?;/\n\t\r,…][(«»<>)";
    $tok =  strtok($fileContent, $separateurs);
    $lemmatisationArray = getWordsLemmatisation();

    while ($tok !== false) {
        $word = checkLemmatisationWord($tok, $lemmatisationArray);
        if ((strlen($word) > 2) && !in_array($word, $arrayEmptyWords)) {
            if (array_key_exists($word, $arrayWordOccurence)) {
                $arrayWordOccurence[$word] += 1;
            } else {
                $arrayWordOccurence[$word] = 1;
            }
        }
        $tok = strtok($separateurs);
    }
    arsort($arrayWordOccurence);
    return $arrayWordOccurence;
}

function getDescriptionTextFile($fileContent){
    $description_page = "";

    $description_page = implode(' ', array_slice(explode(' ', $fileContent), 0, 13));
    if(strlen($fileContent) > 13){
        $description_page .= "</br>" . implode(' ', array_slice(explode(' ', $fileContent), 13, 15));
        if(strlen($fileContent) > 28){
            $description_page .= "...";
        }
    }

    return str_replace("'", "\'", $description_page);
}

// get all data based on the wordToSearch
function getAllPageData($wordToSearch){
    $sqlQuery = "SELECT pageData.id, pageData.fileURL, pageData.fileTitle, pageData.fileDescription, pageData.fileTimestamp, wordList.mot, wordList.idPage, wordList.nbOccurence FROM page_data pageData, word_list wordList WHERE wordList.mot='$wordToSearch' AND pageData.id=wordList.idPage ORDER BY nbOccurence DESC";

    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute();
    return $result->fetchAll();
}

// get all page data
function getPageData(){
    $sqlQuery = "SELECT * FROM page_data ORDER BY id DESC";

    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute();
    return $result->fetchAll();
}


// get page data by url file
function getPageDataByURL($fileURL){
    $sqlQuery = "SELECT * FROM page_data WHERE fileURL='$fileURL'";

    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute();
    return $result->fetchAll();
}

// get word_list data by id
// used to display summary of indexed files in admin panel
function getWordDataPage($pageID){
    $sqlQuery = "SELECT COUNT(*) AS countOccurence, MAX(nbOccurence) AS maxOccurence, mot FROM word_list WHERE idPage=$pageID";

    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute();
    return $result->fetchAll();
}

// check if words are in page based on idPage
function isWordInPage($idPage){
    //$tab_pages = addDataHtmlPageToArray($array_html_links);
    $returnValue = false;

    $sqlQuery = "SELECT * FROM word_list WHERE idPage='$idPage'";
    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute();
    $searchWordPage = $result->fetchAll();

    if(!empty($searchWordPage)){
        $returnValue = true;
    }

    return $returnValue;
}

/************************************************************************/
/************************Database Functions******************************/
/************************************************************************/

// add file data to database
function insertFileData($file, $title, $description, $timestamp){
    $sqlQuery = "INSERT INTO page_data(fileURL, fileTitle, fileDescription, fileTimestamp) VALUES('$file', '$title', '$description', '$timestamp')";
    //echo "insertFileData : " . $sqlQuery . "</br>";
    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute();       
    
    return $GLOBALS["mysqlClient"]->lastInsertId();
}

// add word data to database
function insertWordDataDB($idPage, $arrayPageData){
    foreach ($arrayPageData as $word => $occurence){
        $sqlQuery = "INSERT INTO word_list(mot, nbOccurence, idPage) VALUES('$word', '$occurence', '$idPage')";
        $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
        $result->execute();       
        #return $GLOBALS["mysqlClient"]->lastInsertId();
    }
}

// check if word data already exists in database
function checkDataDB($file, $title, $description, $timestamp, $arrayPageData){
    $fileData = checkFileData($file, $title, $description, $timestamp);
    $idPage = $fileData[0];
    $isInDB = $fileData[1];
    $isTimestampDifferent = $fileData[2];
    // if file is in database, delete all words associated to this file
    if($isInDB){
        // if timestamp is different, delete all words associated to this file and insert new words
        if($isTimestampDifferent){
            deleteWordDataDB($idPage);
            insertWordDataDB($idPage, $arrayPageData);
        }
    }else{
        // insert word data in database if file is not in database
        insertWordDataDB($idPage, $arrayPageData);
    }
}

// check if file is already in database
function checkFileData($fileURL, $title, $description, $timestamp){
    $fileData = getPageDataByURL($fileURL);
    $isInDB = false;
    $isTimestampDifferent = false;
    // if file is in database
    if($fileData){
        $idPage = $fileData[0]["id"];
        if(strval($fileData[0]["fileTimestamp"]) !== strval($timestamp)){
            // update file data in database if timestamp is different
            updateFileData($fileURL, $title, $description, $timestamp);
            $isTimestampDifferent = true;
        }else{
            $isTimestampDifferent = false;
        }
            $isInDB = true;
    } else {
        // insert file data in database
        $idPage = insertFileData($fileURL, $title, $description, $timestamp);
        $isInDB = false;
        
    }
    return array($idPage, $isInDB, $isTimestampDifferent);
}

// update file data in database
function updateFileData($fileURL, $title, $description, $timestamp){
    $sqlQuery = "UPDATE page_data SET fileTitle = '$title', fileDescription = '$description', fileTimestamp = '$timestamp' WHERE fileURL = '$fileURL'";
    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute();       
    return $GLOBALS["mysqlClient"]->lastInsertId();
}

// delete word data from database
function deleteWordDataDB($idPage){
    $sqlQuery = "DELETE FROM word_list WHERE idPage = $idPage";
    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute(); 
}

// truncate tables
function removeDataDB(){
    $sqlQuery = "TRUNCATE TABLE page_data";
    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute();

    $sqlQuery = "TRUNCATE TABLE word_list";
    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute();  
}

/************************************************************************/
/***********************Pagination Functions*****************************/
/************************************************************************/

//fonction qui permet d'obtenir l'url de la page précédente
function getPreviousfileURL(){
    $previousPageNumber = $_GET["numPage"] - 1;
    $baseUrl = strtok($_SERVER['REQUEST_URI'], '&');
    $previousfileURL = $baseUrl . "&numPage=" . $previousPageNumber;
    return $previousfileURL;
}

//fonction qui permet d'obtenir l'url de la page suivante
function getNextfileURL(){
    $nextPageNumber = $_GET["numPage"] + 1;
    $baseUrl = strtok($_SERVER['REQUEST_URI'], '&');
    $nextfileURL = $baseUrl . "&numPage=" . $nextPageNumber;
    return $nextfileURL;
}

//fonction qui permet d'obtenir une url en fonction du numéro de page passé en paramètre
function getfileURLByNumber($numNewPage){
    $baseUrl = strtok($_SERVER['REQUEST_URI'], '&');
    $nextfileURL = $baseUrl . "&numPage=" . $numNewPage;
    return $nextfileURL;
}

//si les données retournés de la requete sont vides, modifie une variable de l'url et affiche la dernière page possédant des résultats 
function verifLastPage($baseURL, $nbPageData){
    if($_GET["numPage"] > $nbPageData){
        $query = $_GET;
        $query['numPage'] = $nbPageData;
        $query_result = http_build_query($query);
        header("LOCATION:" . $baseURL . "?" . $query_result);
        exit();
    }else if($_GET["numPage"] < 1){
        $query = $_GET;
        $query['numPage'] = 1;
        $query_result = http_build_query($query);

        header("LOCATION:" . $baseURL . "?" . $query_result);
        exit();
    }

}

function rebuilURL($wordLike){
    $query = $_GET;
    $query['searchTextInput'] = $wordLike;
    $query_result = http_build_query($query);

    return $query_result;
}

/************************************************************************/
/*************************Display Functions******************************/
/************************************************************************/

function displayWords($pageID){
    $sqlQuery = "SELECT DISTINCT wordList.mot, wordList.nbOccurence FROM page_data pageData, word_list wordList WHERE wordList.idPage='$pageID' ORDER BY nbOccurence DESC";
    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute();
    $wordsOccurence = $result->fetchAll();
    $wordsOccurence = array_slice($wordsOccurence, 0, 20);
    shuffle($wordsOccurence);
    displayArrayWordsOccurence($wordsOccurence, $pageID);
}

function displayArrayWordsOccurence($page, $pageID){
    //$tab_pages = addDataHtmlPageToArray();
    
    #foreach($tab_pages as $page){
        #print_r($page);
        $arrayWords = $page;//getArrayWordsOccurence($page);
        $nbWords = 0;
        echo "<div class='wordCloud' id='wordCloud_" . $pageID . "' style='text-align:center;display:none'>";
        foreach ($arrayWords as $key => $val) {
            #print_r($val);
            if ($nbWords%5 == 0) {
                echo "<br>";
            }
            $textColor = "blue";
            if ($nbWords < 20) {
                $textSize = (($val["nbOccurence"] % 6) * 10) * 1.5;
                /*
                if($textSize < 16){
                    $textColor = "purple";
                }else if($textSize < 31){
                    $textColor = "green";
                }else if($textSize < 46){
                    $textColor = "orange";
                }else if($textSize > 59){
                    $textColor = "red";
                }
                */
                $randomTextColor = ["purple", "green", "orange", "red", "darkblue", "darkgreen", "darkorange", "darkred", "blue"];
                $textColor = $randomTextColor[array_rand($randomTextColor)];
                $word = mb_strtolower($val["mot"], "UTF-8");
                echo "<span value=" . $word ." style='font-size:" . $textSize . "px; display:inline;color:" . $textColor . "'>" . $word . "</span>";
                //onclick='postForm(this)'
                $nbWords += 1;
            }
        }
        echo "</div>";


    #}

}


?>