<?php
include_once("ConnexionPDO.php");
/**
 * Classe de construction des requêtes SQL à envoyer à la BDD
 */
class AccessBDD {
	
    public $login="";
    public $mdp="";
    public $bd="";
    public $serveur="";
    public $port="3306";	
    public $conn = null;

    /**
     * constructeur : demande de connexion à la BDD
     */
    public function __construct(){
        try{
            $this->conn = new ConnexionPDO($this->login, $this->mdp, $this->bd, $this->serveur, $this->port);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function selectLogin($champs) {
        // construction de la requête
         $param = array(
                "name" => $champs["name"],
                "password" => $champs["password"]
        );
        $requete = "SELECT u.id, u.name, u.password FROM utilisateur u ";
        $requete .= "WHERE u.name = :name and u.password = :password ;";
        return $this->conn->query($requete, $champs);
    }

    public function selectAll($champs) {
        $param = array(
                "id" => $champs["id"]
        );
        $requete = "select u.id, u.name ";
        $requete .= "from utilisateur u ";
        $requete .= "where u.id != :id ";
        $requete .= "and u.id NOT IN ( ";
      	$requete .= "select d.id_1 from demande d ";
      	$requete .= "where d.id = :id );";
        return $this->conn->query($requete, $param);
    }

    public function selectContact($champs) {
        $param = array(
                "id" => $champs["id"],
                "name" => $champs["name"],
                "password" => $champs["password"]
        );
        $requete = "select u.id, u.name, u.lat, u.lon, u.time_ ";
        $requete .= "from demande d join utilisateur u2 on d.id = u2.id ";
        $requete .= "join utilisateur u on d.id_1 = u.id ";
        $requete .= "where d.id = :id ";
        $requete .= "and d.accept = 1 ";
        $requete .= "and u2.name = :name ";
        $requete .= "and u2.password = :password ";
        return $this->conn->query($requete, $param);
    }

    public function selectAsk($champs) {
        $param = array(
                "id" => $champs["id"],
                "name" => $champs["name"],
                "password" => $champs["password"]
        );
        $requete = "select u.id, u.name ";
        $requete .= "from demande d join utilisateur u on d.id = u.id ";
        $requete .= "join utilisateur u2 on d.id_1 = u2.id ";
        $requete .= "where d.id_1 = :id ";
        $requete .= "and d.accept = 0 ";
        $requete .= "and u2.name = :name ";
        $requete .= "and u2.password = :password ";
        return $this->conn->query($requete, $param);
    }


        public function selectAttente($champs) {
        $param = array(
                "id" => $champs["id"],
                "name" => $champs["name"],
                "password" => $champs["password"]
        );
        $requete = "select u.id, u.name ";
        $requete .= "from demande d join utilisateur u on d.id_1 = u.id ";
        $requete .= "join utilisateur u2 on d.id = u2.id ";
        $requete .= "where d.id = :id ";
        $requete .= "and d.accept = 0 ";
        $requete .= "and u2.name = :name ";
        $requete .= "and u2.password = :password ";
        return $this->conn->query($requete, $param);
    }

     /**
     * suppresion d'une ou plusieurs lignes dans une table
     * @param string $table nom de la table
     * @param array $champs nom et valeur de chaque champs
     * @return true si la suppression a fonctionné
     */	
    public function delete($table, $champs) {
        if($this->conn != null){
            // construction de la requête
            $requete = "delete from $table where ";
            foreach ($champs as $key => $value) {
                $requete .= "$key=:$key and ";
            }
            // (enlève le dernier and)
            $requete = substr($requete, 0, strlen($requete)-5);
            return $this->conn->execute($requete, $champs);		
        }else{
            return null;
        }
    }

    /**
     * suppresion d'une ou plusieurs lignes dans une table
     * @param string $table nom de la table
     * @param array $champs nom et valeur de chaque champs
     * @return true si la suppression a fonctionné
     */	
    public function delDemande($champs) {
        if($this->conn != null) {
        	$param = array(
                "id" => $champs["id"],
                "id_1" => $champs["id_1"],
                "password" => $champs["password"]
        	);
            // construction de la requête
            $requete = "delete d from demande d join  ";
            $requete .= "utilisateur u on d.id = u.id ";
            $requete .= "where d.id = :id ";
            $requete .= "and d.id_1 = :id_1 ";
            $requete .= "and u.password = :password ;";
            return $this->conn->execute($requete, $param);		
        }else{
            return null;
        }
    }


    /**
     * ajout d'une ligne dans une table
     * @param string $table nom de la table
     * @param array $champs nom et valeur de chaque champs de la ligne
     * @return true si l'ajout a fonctionné
     */	
    public function insertOne($table, $champs) {
        if($this->conn != null && $champs != null) {
            // construction de la requête
            $requete = "insert into $table (";
            foreach ($champs as $key => $value){
                $requete .= "$key,";
            }
            // (enlève la dernière virgule)
            $requete = substr($requete, 0, strlen($requete)-1);
            $requete .= ") values (";
            foreach ($champs as $key => $value){
                $requete .= ":$key,";
            }
            // (enlève la dernière virgule)
            $requete = substr($requete, 0, strlen($requete)-1);
            $requete .= ");";
            return $this->conn->execute($requete, $champs);		
        }else{
            return null;
        }
    }

    /**
     * Ajout de l'entitée composée livre dans la bdd
     *
     * @param [type] $champs nom et valeur de chaque champs de la ligne
     * @return true si l'ajout a fonctionné
     */
    public function insertDemande($champs) {
        if($this->conn != null && $champs != null) {
        	$param = array(
                "id" => $champs["id"],
                "id_1" => $champs["id_1"],
                "password" => $champs["password"]
        	);
            // construction de la requête
            $requete = "insert into demande (id, id_1, accept) ";
            $requete .= "SELECT :id, :id_1, 0 ";
            $requete .= "from utilisateur u ";
            $requete .= "where u.id = :id and u.password = :password;";
          	echo $requete;
            return $this->conn->execute($requete, $param);		
        }else{
            return null;
        }
    }


    /**
     * modification d'une ligne dans une table
     * @param string $table nom de la table
     * @param string $id id de la ligne à modifier
     * @param array $param nom et valeur de chaque champs de la ligne
     * @return true si la modification a fonctionné
     */
    public function updateOne($table, $id, $champs, $numero = null) {
        if($this->conn != null && $champs != null){
            // construction de la requête
            $requete = "update $table set ";
            foreach ($champs as $key => $value){
                $requete .= "$key=:$key,";
            }
            // (enlève la dernière virgule)
            $requete = substr($requete, 0, strlen($requete)-1);				
            $champs["id"] = $id;
            $requete .= " where id=:id;";
            if($numero != null)
            {
                $requete = substr($requete, 0, strlen($requete)-1);				
                $champs["numero"] = $numero;
                $requete .= " and numero=:numero;";
            }				
            return $this->conn->execute($requete, $champs);		
        }else{
            return null;
        }
    }

     /**
     * Modification de l'entitée composée livre dans la bdd
     *
     * @param [type] $champs nom et valeur de chaque champs de la ligne
     * @param [type] $id de l'element
     * @return true si l'ajout a fonctionné
     */
    public function validDemande($champs){
        if($this->conn != null && $champs != null) {
            $param = array(
                "id" => $champs["id"],
                "id_1" => $champs["id_1"],
                "password" => $champs["password"]
        );
            // construction de la requête
            $requete = "update demande d ";
            $requete .= "join utilisateur u on d.id_1 = u.id ";
            $requete .= "set accept = 1";				
            $requete .= " where d.id_1 = :id and d.id = :id_1 and u.password = :password;";
			echo $requete;
            return $this->conn->execute($requete, $param);		
        }else{
            return null;
        }
    }

	 /**
     * Modification de l'entitée composée livre dans la bdd
     *
     * @param [type] $champs nom et valeur de chaque champs de la ligne
     * @param [type] $id de l'element
     * @return true si l'ajout a fonctionné
     */
    public function validActu($champs){
        if($this->conn != null && $champs != null) {
            $param = array(
                "id" => $champs["id"],
                "password" => $champs["password"],
              	"lat" => $champs["lat"],
                "lon" => $champs["lon"],
                "time_" => $champs["time_"]
        	);
            // construction de la requête
            $requete = "update utilisateur ";
            $requete .= "set lat = :lat, lon = :lon, time_ = :time_ ";				
            $requete .= "where id = :id and password = :password";
			echo $requete;
            return $this->conn->execute($requete, $param);		
        }else{
            return null;
        }
    }
}