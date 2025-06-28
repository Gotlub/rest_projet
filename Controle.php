<?php
include_once("AccessBDD.php");

/**
 * Contrôleur : reçoit et traite les demandes du point d'entrée
 */
class Controle{
	
    private $accessBDD;

    /**
     * Constructeur : récupération de l'instance d'accès à la BDD
     */
    public function __construct(){
        try{
            $this->accessBDD = new AccessBDD();
        }catch(Exception $e){
            $this->reponse(500, "erreur serveur");
            die();
        }
    }

    /**
     * réponse renvoyée (affichée) au client au format json
     * @param int $code code standard HTTP
     * @param string $message message correspondant au code
     * @param array $result résultat de la demande 
     */
	private function reponse($code, $message, $result=""){
        $retour = $result;
        echo json_encode($retour, JSON_UNESCAPED_UNICODE);
    }

    /**
     * requete arrivée en GET (select)
     * @param string $table nom de la table
     * @param type $champs nom et valeur des champs de recherche
     */
    public function get($table, $champs){
        $result = null;
        if ($table=="login") {
            $result = $this->accessBDD->selectLogin($champs);
        }else if ($table=="contact") {
            //les demandes acceptées -> listUtilisateur lat lon time
            $result = $this->accessBDD->selectContact($champs);
        }else if ($table=="demande") {
            //les demandes en cours , utilisateur = id_1
            // return list id nom
            $result = $this->accessBDD->selectAsk($champs);
        }else if ($table=="attente") {
            //les demandes en cours , utilisateur = id_1
            // return list id nom
            $result = $this->accessBDD->selectAttente($champs);
        }else if ($table=="all") {
            //les demandes en cours -> utilisateur = id_1
            $result = $this->accessBDD->selectAll($champs);
        }
        if (gettype($result) != "array" && ($result == false || $result == null)){
            $this->reponse(400, "requete invalide");
        }else{	
            $this->reponse(200, "OK", $result);
        }
    }

    /**
     * requete arrivée en DELETE
     * @param string $table nom de la table
     * @param array $champs nom et valeur des champs
     */
    public function delete($table, $champs){
        $result = null;
        if($table = "demande"){
            $result = $this->accessBDD->delDemande($champs);
        }
      	if ($result == null || $result == false ){
            $this->reponse(400, "requete invalide");
        }else{	
            $this->reponse(200, "OK");
        }
    }

    /**
     * requete arrivée en POST (insert)
     * @param string $table nom de la table
     * @param array $champs nom et valeur des champs
     */
    public function post($table, $champs){
        $result = null;
        if ($table == "demande"){
            $result = $this->accessBDD->insertDemande($champs);
        }else if ($table == "compte"){
          	$result = $this->accessBDD->insertCompte($champs);
        }
      	if ($result == null || $result == false){
            $this->reponse(400, "requete invalide");
        }else{	
            $this->reponse(200, "OK");
        }
    }


    /**
     * requete arrivée en PUT (update)
     * @param string $table nom de la table
     * @param string $id valeur de l'id
     * @param array $champs nom et valeur des champs
     */
    public function put($table, $champs){
        $result = null;
        //TODO
        if ($table == "accept"){
            $result = $this->accessBDD->validDemande($champs);
        }else if ($table == "actu"){
            $result = $this->accessBDD->validActu($champs);
        }
      	if ($result == null || $result == false){
            $this->reponse(400, "requete invalide");
        }else{	
            $this->reponse(200, "OK");
        }
    }

	
    /**
     * login et/ou pwd incorrects
     */
    public function unauthorized(){
        $this->reponse(401, "authentification incorrecte");
    }
}