<?php

$pathParent = dirname(__FILE__);

include $pathParent . "/credentials/credentials.php";
header('Content-Type: text/html; charset=utf-8');
$path = $pathParent . "/fichiers_txt";


function addHtmlLinksToArray(){
    $array_html_links = array();
    $html_links_file = file_get_contents($GLOBALS["pathParent"] . '/html_links/html_links_file.txt');

    $separateurs =  "\n";
    $tok =  strtok($html_links_file, $separateurs);

    while ($tok !== false) {
        //echo $tok . " || ";
        array_push($array_html_links, $tok);
        $tok = strtok($separateurs);
    }
    return $array_html_links;
}

function addDataHtmlPageToArray(){
    $array_html_data = array();
    $dataPage = array();
    $array_html_links = addHtmlLinksToArray();
    foreach($array_html_links as $urlPage){
        $dataPage["URL"] = $urlPage;
        $dataPage["Title"] = getTitle($urlPage);
        $dataPage["MetaData"] = getMetaData($urlPage);
        $dataPage["BodyContent"] = getBodyContent($urlPage);

        $array_html_data[$urlPage] = array($dataPage);
    }
    return $array_html_data;
}


//function to get all the HTML code of the URL passed in parameter
function get_html_data($url) {
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
  }

//function that returns the title of the html data page 
function getTitle($url) {
    $data = file_get_contents($url);
    $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $data, $matches) ? $matches[1] : null;
    return $title;
}

//function that returns the title of the html data page 
function getMetaData($url) {
    $metaTags = get_meta_tags($url);
    return $metaTags;
}
/*
// function to get <p> in body
// /!\ but not working great
function getBody($url){
    $data = file_get_contents($url);
    $bodyContent = strip_tags($data, '<p>');

    return $bodyContent;
}
*/

// function to get all <p> in body
function getBodycontent($url){
    $data = file_get_contents($url);
    $bodyContent = "";

    $dom = new DOMDocument;
    @$dom->loadHTML($data);
    foreach ($dom->getElementsByTagName('p') as $tag) {
        $bodyContent = $bodyContent . $tag->nodeValue; // concat all paragraphs together and return it
    }
    
    return $bodyContent;
}

function getWordOccurence($data){
    $tab_occurence = array();
    $separateur = "'’;. -/\n\t\r,…][(«»<>)";
    $tabs_mots_vide = addEmptyWordToArray();

    
        #foreach($val as $value){


        #}
        /*
        $tok =  strtok($val, $separateur);

        while ($tok !== false) {
            if ((strlen($tok) > 2) && !in_array($tok, $tabs_mots_vide)) {
                if (array_key_exists($tok, $tab_occurence)) {
                    $tab_occurence[$tok] += 1;
                } else {
                    $tab_occurence[$tok] = 1;
                }
            }
            $tok = strtok($separateur);
        }
        arsort($tab_occurence);
        */

    return $tab_occurence;

}

function getPageDescription($page){
    $description_page = "";
    if (array_key_exists("description", $page[0]["MetaData"])) {
        $description_page = implode(' ', array_slice(explode(' ', $page[0]["MetaData"]["description"]), 0, 13)) . "</br>" . implode(' ', array_slice(explode(' ', $page[0]["MetaData"]["description"]), 13, 15)) . "...";
        //echo $pageData[0]["MetaData"]["description"];
    } else {
        $description_page = implode(' ', array_slice(explode(' ', $page[0]["BodyContent"]), 0, 13)) . "</br>" . implode(' ', array_slice(explode(' ', $page[0]["BodyContent"]), 13, 15)) . "...";
        //echo mb_substr($bodyPage, 0, 80) . "</br>" . mb_substr($bodyPage, 80, 80) . "...";
    }
    return $description_page;
}


/************************************************************************/

function displayArrayWordsOccurence($page){
    //$tab_pages = addDataHtmlPageToArray();
    
    #foreach($tab_pages as $page){
        #print_r($page);
        $arrayWords = $page;//getArrayWordsOccurence($page);
        $nbWords = 0;
        echo "<div>";
        foreach ($arrayWords as $key => $val) {
            #print_r($val);
            if ($nbWords == 10) {
                echo "<br>";
            }
            if ($nbWords < 20) {
                $textSize = ($val["nbOccurence"] % 6) * 10;
                echo "<span style='font-size:" . $textSize . "px; display:inline;'>" . $val["mot"] . "</span>";
                $nbWords += 1;
            }
        }
        echo "</div>";


    #}

}

function getArrayWordsOccurence($page){
    $tab_occurence_head = array();
    $tab_occurence_body = array();

    $pageMetaData = "";

    $pageURL  = $page["URL"];
    $pageTitle  = $page[0]["Title"];
    $pageDescription  = getPageDescription($page);

    foreach ($page[0] as $val) {
        if (is_array($val)) {
            foreach ($val as $value) {
                $pageMetaData .= $value . ";";
            }
        }
    }
    //$tab_occurence_head.array_push($pageTitle);
    //$tab_occurence_head.array_push($pageDescription);

    $tab_occurence_head = array($pageTitle, $pageDescription, $pageMetaData);
    $tab_occurence_head = getTabOccurenceHead($tab_occurence_head);
    #print_r($tab_occurence_head);

    #echo "|||| BODY ||||";

    $tab_occurence_body = getTabOccurenceBody($page[0]["BodyContent"]);
    #print_r($tab_occurence_body);

    #echo "<br><br>";

    $arrayWords = concatArrayOccurenceHeadBody($tab_occurence_head, $tab_occurence_body);
    return $arrayWords;
}

function getTabOccurenceHead($tab_array_head){
    $separateur = "'’;. —-_/\n\t\r,…=:][(«»<>)?!";
    $tabs_mots_vide = addEmptyWordToArray();
    $tab_occurence_head = array();

    foreach($tab_array_head as $headElement){
        $headElement = preg_replace('/[0-9]+/', '', $headElement);
        $tok =  strtok($headElement, $separateur);

        while ($tok !== false) {
            if ((strlen($tok) > 2) && !in_array($tok, $tabs_mots_vide)) {
                if (array_key_exists($tok, $tab_occurence_head)) {
                    $tab_occurence_head[$tok] += 2;
                } else {
                    $tab_occurence_head[$tok] = 2;
                }
            }
            $tok = strtok($separateur);
        }
    }
    arsort($tab_occurence_head);
    return $tab_occurence_head;

}

function getTabOccurenceBody($bodyContent)
{
    #echo $bodyContent;
    $separateur = "'’;. —-_/\n\t\r,…=:][(«»<>)?!";
    $tabs_mots_vide = addEmptyWordToArray();
    $tab_occurence_body = array();

    $bodyContent = preg_replace('/[0-9]+/', '', $bodyContent);

    $tok =  strtok($bodyContent, $separateur);


    while ($tok !== false) {
        if ((strlen($tok) > 2) && !in_array($tok, $tabs_mots_vide)) {
            if (array_key_exists($tok, $tab_occurence_body)) {
                $tab_occurence_body[$tok] += 1;
            } else {
                $tab_occurence_body[$tok] = 1;
            }
        }
        $tok = strtok($separateur);
    }

    arsort($tab_occurence_body);
    return $tab_occurence_body;
}

function saveDataPage(){
        $tab_pages = addDataHtmlPageToArray();

        foreach($tab_pages as $page){
            $pageURL  = $page[0]["URL"];
            $pageTitle  = $page[0]["Title"];
            $pageDescription  = getPageDescription($page);

            $arrayWordOccurence = getArrayWordsOccurence($page);

            $sqlQuery = "INSERT INTO page_data(pageURL, pageTitle, pageDescription) VALUES('$pageURL', '$pageTitle', '$pageDescription')";
            $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
            $result->execute();
            $pageID = $GLOBALS["mysqlClient"]->lastInsertId();
            echo $pageID;

            foreach($arrayWordOccurence as $word=>$nb_occur){
                echo $word . ": ";
                echo $nb_occur;
                echo "||||";
                
                $sqlQuery = "INSERT INTO word_list(mot, nbOccurence, idPage) VALUES('$word', '$nb_occur', $pageID)";
                $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
                $result->execute();
                
            }
            
        }
    
        

}

//permet de supprimer le contenu de toutes les tables 
function removeDataPage()
{
    $sqlQuery = "TRUNCATE TABLE word_list";
    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute();

    $sqlQuery = "TRUNCATE TABLE page_data";
    $result = $GLOBALS["mysqlClient"]->prepare($sqlQuery);
    $result->execute();
}

function concatArrayOccurenceHeadBody($tab_occurence_head, $tab_occurence_body){
    foreach(array_keys($tab_occurence_head + $tab_occurence_body) as $key){
        $tab_occurence_total[$key] = @($tab_occurence_head[$key] + $tab_occurence_body[$key]);
    }

    return $tab_occurence_total;
    /*
    foreach($tab_occurence_head as $keyHead=>$valHead){
        foreach($tab_occurence_body as $keyBody=>$valBody){

        }
    }
    */
}

?>