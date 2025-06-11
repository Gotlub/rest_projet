<?php
header('Content-Type: application/json');
include_once("Controle.php");
$controle = new Controle();

// Contrôle de l'authentification
if(!isset($_SERVER['PHP_AUTH_USER']) || (isset($_SERVER['PHP_AUTH_USER']) && 
        !(($_SERVER['PHP_AUTH_USER']=='admin' && ($_SERVER['PHP_AUTH_PW']=='adminpwd'))))){
    $controle->unauthorized();
    
}else{
    if(isset($_GET['error']) && $_GET['error'] == 404){
        echo json_encode(array("message" => "404 Not Found "));
        exit();
    }
    // récupération des données
    // Nom de la table au format string
    $table = filter_input(INPUT_GET, 'table', FILTER_SANITIZE_STRING) ??
             filter_input(INPUT_POST, 'table', FILTER_SANITIZE_STRING);
    // nom et valeur des champs au format json
    $champs = filter_input(INPUT_GET, 'champs', FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES) ??
               filter_input(INPUT_POST, 'champs', FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
    if($champs != ""){
        echo " champs " . $champs;
        $champs = json_decode($champs, true);
        //$champs = str_replace("-", " ", $champs); //c'est tres tres sale
    }


    //echo "table " . $table  . " champs " . $champs;
    // traitement suivant le verbe HTTP utilisé
    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        echo("get");
        $controle->get($table, $champs);
    }else if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $controle->post($table, $champs);
    }else if($_SERVER['REQUEST_METHOD'] === 'PUT'){
        $controle->put($table, $champs);
    }else if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
        $controle->delete($table, $champs);
    }

}