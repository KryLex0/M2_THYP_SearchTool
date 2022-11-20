<?php

$pathParent = dirname(__FILE__);

include $pathParent . "/credentials/credentials.php";
include $pathParent . "/explore_dir_recursiv.php";
header('Content-Type: text/html; charset=utf-8');
$path = $pathParent . "/fichiers_txt";

$mysqlClient = $mysqlClient;

//type de fichier prit en charge
$extensionAccepte = array("txt", "html");

//lancement de la fonction qui explore tout les dossiers dans le répertoire "docs"
//explorerDir($path, $extensionAccepte);
$arrayPathFile = array();

$fileFolder = "file_folder";

function getFileContent($path){
    $fileContent = file_get_contents($path);
    return $fileContent;
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
    $fichier_mots_vide = strtolower(file_get_contents($GLOBALS["pathParent"] . '/mots_vide/fichier_mots_vide.txt'));

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

    while ($tok !== false) {
        $word = mb_strtolower($tok, 'UTF-8');
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

    foreach ($metaDataPage as $metaDataKey => $metaDataValue){
        $tok =  strtok($metaDataValue, $separateurs);

        while ($tok !== false) {
            $word = mb_strtolower($tok, 'UTF-8');
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

    while ($tok !== false) {
        $word = mb_strtolower($tok, 'UTF-8');
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
        if(strpos($file, ".html")){
            $fileContent = getFileContent($file);
            #echo $fileContent;
            $titlePage = getTitlePage($file);
            $metaDataPage = getMetaDataPage($file);
            $bodyContentPage = getBodyContentPage($file);
            $descriptionPage = getDescriptionPage($metaDataPage, $bodyContentPage);
            #echo $title . "::" . $bodyContent . "</br>";
            #print_r($metaData);
            $arrayPageData = getArrayFromPageData($titlePage, $bodyContentPage, $metaDataPage);
            insertDataDB($file, $titlePage, $descriptionPage, $arrayPageData);
        }
        else if(strpos($file, ".txt")){
            $arraTextDataFile = getArrayFromTextFile($file);
            $titleFile = "Fichier Texte: " . $file;
            $descriptionFile = "Ce fichier texte ne contient pas de description.";
            insertDataDB($file, $titleFile, $descriptionFile, $arraTextDataFile);

        }

    }
}

function getArrayFromTextFile($file){
    $arrayWordOccurence = array();
    $arrayEmptyWords = addEmptyWordToArray();

    $fileContent = file_get_contents($file);
    $separateurs =  "'`’\". -+=*!?;/\n\t\r,…][(«»<>)";
    $tok =  strtok($fileContent, $separateurs);

    while ($tok !== false) {
        $word = mb_strtolower($tok, 'UTF-8');
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

function insertFileData($file, $title, $description){
    $sqlQuery = "INSERT INTO page_data(pageURL, pageTitle, pageDescription) VALUES('$file', '$title', '$description')";
    echo "insertFileData : " . $sqlQuery . "</br>";
    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute();       
    
    return $GLOBALS["mysqlClient"]->lastInsertId();
}


function insertDataDB($file, $title, $description, $arrayPageData){
    $idPage = insertFileData($file, $title, $description);
    foreach ($arrayPageData as $word => $occurence){
        $sqlQuery = "INSERT INTO word_list(mot, nbOccurence, idPage) VALUES('$word', '$occurence', '$idPage')";
        $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
        $result->execute();       
        #return $GLOBALS["mysqlClient"]->lastInsertId();
    }
}


function removeDataDB(){
    $sqlQuery = "TRUNCATE TABLE page_data";
    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute();

    $sqlQuery = "TRUNCATE TABLE word_list";
    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute();  
}

function getAllPageData($wordToSearch){
    $sqlQuery = "SELECT pageData.id, pageData.pageURL, pageData.pageTitle, pageData.pageDescription, wordList.mot, wordList.idPage FROM page_data pageData, word_list wordList WHERE wordList.mot='$wordToSearch' AND pageData.id=wordList.idPage ORDER BY nbOccurence DESC";

    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute();
    return $result->fetchAll();
}

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

function displayWords($pageID){
    $sqlQuery = "SELECT DISTINCT wordList.mot, wordList.nbOccurence FROM page_data pageData, word_list wordList WHERE wordList.idPage='$pageID' ORDER BY nbOccurence DESC";
    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute();
    $wordsOccurence = $result->fetchAll();
    $wordsOccurence = array_slice($wordsOccurence, 0, 20);
    shuffle($wordsOccurence);
    displayArrayWordsOccurence($wordsOccurence, $pageID);
}

/************************************************************************/


function displayArrayWordsOccurence($page, $pageID){
    //$tab_pages = addDataHtmlPageToArray();
    
    #foreach($tab_pages as $page){
        #print_r($page);
        $arrayWords = $page;//getArrayWordsOccurence($page);
        $nbWords = 0;
        echo "<div class='wordCloud' id='wordCloud_" . $pageID . "' style='text-align:center;display:none'>";
        foreach ($arrayWords as $key => $val) {
            #print_r($val);
            if ($nbWords == 10) {
                echo "<br>";
            }
            $textColor = "blue";
            if ($nbWords < 20) {
                $textSize = (($val["nbOccurence"] % 6) * 10) * 1.5;
                if($textSize < 16){
                    $textColor = "purple";
                }else if($textSize < 31){
                    $textColor = "green";
                }else if($textSize < 46){
                    $textColor = "orange";
                }else if($textSize > 59){
                    $textColor = "red";
                }
                echo "<span style='font-size:" . $textSize . "px; display:inline;color:" . $textColor . "'>" . $val["mot"] . "</span>";
                $nbWords += 1;
            }
        }
        echo "</div>";


    #}

}


?>