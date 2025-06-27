<?php
header('Content-Type: application/json');
include_once("Controle.php");

$controle = new Controle();
// Contrôle de l'authentification
// récupération des données
// Nom de la table au format string
$table = filter_input(INPUT_GET, 'table', FILTER_UNSAFE_RAW ) ??
        filter_input(INPUT_POST, 'table', FILTER_UNSAFE_RAW );
// nom et valeur des champs au format json
$champs = filter_input(INPUT_GET, 'champs', FILTER_UNSAFE_RAW ,FILTER_FLAG_NO_ENCODE_QUOTES) ??
               filter_input(INPUT_POST, 'champs', FILTER_UNSAFE_RAW ,FILTER_FLAG_NO_ENCODE_QUOTES);
if($champs != ""){
   $champs = json_decode($champs, true);
}


// traitement suivant le verbe HTTP utilisé
if($_SERVER['REQUEST_METHOD'] === 'GET'){
  $controle->get($table, $champs);
 }else if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $controle->post($table, $champs);
}else if($_SERVER['REQUEST_METHOD'] === 'PUT'){
  $controle->put($table, $champs);
}else if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
  $controle->delete($table, $champs);
 }
	

