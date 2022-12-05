<?php

ini_set("allow_url_fopen ", false);
$pathParent = dirname(__FILE__);
include $pathParent . "/credentials/credentials.php";
#require $pathParent . "/phpScript/save_bdd.php";
#require $pathParent . "/phpScript/html_functions.php";
require $pathParent . "/phpScript/html_data_functions.php";

header('Content-Type: text/html; charset=utf-8');

$pathFileFolder = "file_folder";
//taille max de 8 Mo
$sizeMax = 8;
$tailleMaxFichier = $sizeMax * 1024 * 1024;

//type de fichier prit en charge
$extensionAccepte = array("html", "txt", "pdf");

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

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">

<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>



<a href="index.php">Search Word Tool</a>
<center><h1>Interface d'indexation</h1></center>


<div class="divSearchBar">
    <button onclick="addDataBDD()" class="btn btn-info">Indexer les documents</button>
    <button onclick="removeDataBDD()" class="btn btn-info">Supprimer les données d'indexation</button>
    <!-- <button onclick="scanDataBDD()" class="btn btn-info">Scanner le répertoire</button> -->

    <!-- <button onclick="updateDataBDD()">Vérifier données BDD</button> -->
    <div>
        <p id="operation_BDD"></p>
        <center>
            <div class="spinner-border text-primary loading" id="loading" style="display:none;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </center>
        <p id="output_files"></p>

    </div>
</div>

<div class="divSearchBar" style="text-align:left;float:left;margin-left:2%;">
    <h5>Arborescence des documents</h5>

    <?php

    $dir = new RecursiveDirectoryIterator($pathFileFolder, FilesystemIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::SELF_FIRST);
    $arrayFiles = array();
    $fileRoot = true;
    while ($files->valid()) {
        $file = $files->current();
        $filename = $file->getFilename();
        $deep = $files->getDepth();
        $indent = str_repeat('│ ', $deep);
        $files->next();
        if ($fileRoot) {
            $files->next();
            $fileRoot = false;
        }
        $valid = $files->valid();
        if ($valid and ($files->getDepth() - 1 == $deep or $files->getDepth() == $deep)) {
    ?>
            <ion-icon style="display:none" onClick='deleteDir("<?php echo $file; ?>")' name='close-circle-outline'></ion-icon>
            <?php echo $indent; ?>├ <a href='<?php echo $file; ?>'>
                <!-- <ion-icon name='folder-outline'></ion-icon> -->
                <?php echo $filename; ?>
            </a><br>
        <?php
            //echo "<ion-icon onClick='deleteDir(".strval($file).")' name='close-circle-outline'></ion-icon>$indent", "├ <a href='$file'> <ion-icon name='folder-outline'></ion-icon> $filename</a><br>";
        } else {
        ?>
            <ion-icon style="display:none" onClick='deleteDir("<?php echo $file; ?>")' name='close-circle-outline'></ion-icon>
            <?php echo $indent; ?>└ <a href='<?php echo $file; ?>'>
                <!-- style='background-color:#b5f3fd;'> -->
                <!-- <ion-icon name='document-outline'>    -->
                </ion-icon><?php echo $filename; ?>
            </a><br>
    <?php
            //echo "<ion-icon onClick='deleteDir('".$file."')' name='close-circle-outline'></ion-icon>$indent", "└ <a href='$file' style='background-color:#b5f3fd;'> <ion-icon name='document-outline'></ion-icon> $filename</a><br>";
        }
    }
    /*
$recursiveTreeIterator = new RecursiveTreeIterator(new RecursiveDirectoryIterator($pathFileFolder, RecursiveDirectoryIterator::SKIP_DOTS));
foreach($recursiveTreeIterator as $recursivePath) {
    $indent = str_repeat('   ', $recursiveTreeIterator->getDepth());
    echo $indent, " ├ $recursivePath\n";//echo $recursivePath."<br>";
}
*/
    ?>

</div>

<?php
//print_r($_FILES);

$pageData = getPageData();
$nbFilesIndexed = count($pageData);
if ($pageData) {

?>
    <center>
        <div style="display: inline-block;margin-left:-16%;" class="divSearchBar">
            <h5>Il y a un total de <?php echo $nbFilesIndexed;
                                    if ($nbFilesIndexed == 1) {
                                        echo " document indexé.";
                                    } else {
                                        echo " documents indexées.";
                                    } ?></h5>
            </br>
            <table class="table table-striped table-hover table-bordered" id="dataTable">
                <thead>
                    <tr>
                        <th scope="col">Document</th>
                        <th scope="col">Nombre de mots indexés</th>
                        <th scope="col">Max occurence</th>
                        <th scope="col">Mot le plus présent</th>
                    </tr>
                </thead>
                <tbody>


                <?php


                //print_r($pageData);
                $i = 0;
                foreach ($pageData as $key => $val) {
                    //print_r($val);
                    $pageID = $val['id'];
                    $pageName = str_replace("../", "", $val['fileURL']);

                    $wordDataPage = getWordDataPage($pageID);
                    //print_r($wordDataPage);
                    echo "<tr data-index='$i'><th scope='row'>$pageName</th><td>" . $wordDataPage[0]["countOccurence"] . "</td><td>" . $wordDataPage[0]["maxOccurence"] . "</td><td>" . $wordDataPage[0]["mot"] . "</td></tr>";
                    $i++;
                }
            }

                ?>
                </tr>
                </tbody>
            </table>
        </div>
    </center>


    <!--Formulaire d'upload d'une image-->

    <div class="divSearchBar">
        <h5>Upload</h5>
        <div class="divContainer">
            <form action="admin_page.php" method="POST" enctype="multipart/form-data">
                <!-- <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ($tailleMaxFichier); ?>"> -->
                <!-- <label>Votre fichier: </label><button onclick="document.getElementById('fileData').click()">Selectionnez un fichier</button> -->
                <table>
                    <tr>
                        <td><label>Votre fichier: </label></td>
                        <td><input id="fileData" type="file" name="fileData[]" multiple></td>
                    </tr>


                    <!-- <label>Votre dossier: </label><button onclick="document.getElementById('folderData').click()">Selectionnez un dossier</button> -->
                    <tr>
                        <td><label>Votre dossier: </label></td>
                        <td><input id="folderData" type="file" name="fileData[]" multiple directory="" webkitdirectory="" moxdirectory=""></td>
                    </tr>


                    <tr>
                        <td><label>Chemin des fichiers: </label></td>
                        <td><input type="text" name="newPath" placeholder="Racine si vide"></td>
                    </tr>
                </table>

                <br><br>
                <input type="submit" class="btn btn-info" value="Envoyer" name="uploadFile">
            </form>
        </div>
        <p>Type de fichier prit en charge:
            <!--Affiche les extensions prises en charge avec une virgule pour séparer (sauf le dernier élément)-->
            <?php foreach ($extensionAccepte as $val) {
                if (array_search($val, $extensionAccepte) == array_key_last($extensionAccepte)) {
                    echo $val;
                } else {
                    echo $val . ", ";
                }
            }
            echo "</br>Taille max d'un document: $sizeMax Mo";
            ?>
        </p>
    </div>

    <?php
    // $arrayDataFile = [];

    if (isset($_FILES['fileData'])) {
        if ($_POST["newPath"] != "") {
            $newPath = $pathFileFolder . "/" . $_POST["newPath"] . "/";
            if (is_dir($newPath)) {
                echo "Le dossier existe";
            } else {
                mkdir($newPath, 0777, true);
            }
        } else {
            $newPath = "file_folder/";
        }

        $countfiles = count($_FILES['fileData']['name']);
        echo $countfiles;
        // Looping all files
        for ($i = 0; $i < $countfiles; $i++) {
            $filename = $_FILES['fileData']['name'][$i];
            $filePath = $_FILES['fileData']['tmp_name'][$i];

            // $arrayDataFile[$i] = [
            //     "name" => $filename,
            //     "path" => $filePath
            // ];

            // Upload file
            move_uploaded_file($filePath, $newPath . $filename);
        }
    }


    ?>