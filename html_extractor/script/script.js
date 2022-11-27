function addDataBDD() {
    document.getElementById("operation_BDD").innerHTML = "Sauvegarde des données en cours...";
    $.ajax({
        type: "POST",
        url: "phpScript/add_data.php",
        data:{action:'addSuccess'},
        //success:function(azerty) {
        }).done(function (data) {

        //resultAction();
            console.log(data);
            resultAction(data);
            alert("Les données des différents fichiers ont bien été sauvegardé dans la base de données !")
            document.getElementById("operation_BDD").innerHTML = "Sauvegarde des données terminée !";

            //console.log(html);

     });

}

function removeDataBDD() {
    document.getElementById("operation_BDD").innerHTML = "Suppression des données en cours...";

    $.ajax({
        type: "POST",
        url: "phpScript/remove_data.php",
        data:{action:'removeSuccess'},
        //success:function(html) {
        }).done(function (data) {
            resultAction(data);

            alert("Les données présentes dans la BDD ont bien été supprimés !")
            document.getElementById("operation_BDD").innerHTML = "Suppression des données terminée !";

            //console.log(html);
    });
}

function resultAction(data){
    //alert("test");
    document.getElementById("output_files").innerHTML = "";
    document.getElementById("output_files").innerHTML += data + "<br>";
}

function postForm(word){
    console.log(word);
    $.ajax({
        url: 'index.php',
        type: 'POST',
        data: {
            searchTextInput: word,
            action: 'resendForm'
        },
        success: function(msg) {
            //alert('Email Sent');
        }
    });
    window.location = "index.php?searchTextInput=" + word;

}

function updateDataBDD() {
    $.ajax({
        type: "POST",
        url: "index.php",
        data:{action:'updateSuccess'},
        success:function(html) {
            alert("Les données présentes dans la BDD ont bien été mise à jour !")
            //console.log(html);
        }
    });
}

function pageAccessPassword() {
    let text;
    let password = prompt("Veuillez saisir le mot de passe:", "");
    if(password == "admin"){
        alert("Mot de passe correct !");
        window.location = "admin_page.php";
    }else {
        alert("Mot de passe incorrect !");
    }
    //document.getElementById("demo").innerHTML = text;
}


function test(urlTxt) {
    $.ajax({
        type: "POST",
        url: "index.php",
        data:{action:'updateSuccess'},
        success:function(html) {
            var urlSite = window.parent.location.href
            urlSite = urlSite.slice(0, urlSite.lastIndexOf('/'));
            urlSite = urlSite + "/fichiers_txt/" + urlTxt.id
            window.open(urlSite, "_blank")//window.location = urlSite
            //console.log(urlSite)//urlTxt.id)
            //console.log(html);
        }
    });
}

function displayWordClound(pageID){
    var wordCloudToDisplay = "wordCloud_" + pageID;
    //var list = document.getElementsByClassName("wordCloud");
    const list = document.querySelectorAll(".wordCloud");

    if(document.getElementById(wordCloudToDisplay).style.display == "block"){

        list.forEach((item)=>{
            item.style.display = "none"
        });
        
        document.getElementById(wordCloudToDisplay).style.display = "none";
    }else if(document.getElementById(wordCloudToDisplay).style.display == "none") {
        list.forEach((item)=>{
        if(item.id == wordCloudToDisplay){
            item.style.display = "block"
        }else{
            item.style.display = "none"
        }
    });
        document.getElementById(wordCloudToDisplay).style.display = "block";
    }

    

    
    
}