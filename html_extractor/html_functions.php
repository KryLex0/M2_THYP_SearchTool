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


?>