<?php
include_once("ConnexionPDO.php");
/**
 * Classe de construction des requêtes SQL à envoyer à la BDD
 */
class AccessBDD {
	
    public $login="root";
    public $mdp="";
    public $bd="projet";
    public $serveur="localhost";
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

    public function selectLogin($champs){
        echo "selectLogin";
        // construction de la requête
        $requete = "select * from utilisateur where ";
        foreach ($champs as $key => $value){
            $requete .= "$key=:$key and ";
        }
        // (enlève le dernier and)
        $requete = substr($requete, 0, strlen($requete)-4);
        return $this->conn->query($requete, $champs);
    }

    public function selectAll($champs){
        $param = array(
                "id" => $champs["id"]
        );
        $requete = "select id, name ";
        $requete .= "from utilisateur ";
        $requete .= "where id != :id ";
        return $this->conn->query($requete, $param);
    }

    public function selectContact($champs){
        $param = array(
                "id" => $champs["id"]
        );
        $requete = "select u.id, u.name, u.lat, u.lon, u.time_ ";
        $requete .= "from demande d join utilisateur u on d.id = u.id ";
        $requete .= "where d.id_1 = :id ";
        $requete .= "and d.accept = 1";
        return $this->conn->query($requete, $param);
    }

    public function selectAsk($champs){
        $param = array(
                "id" => $champs["id"]
        );
        $requete = "select u.id, u.name ";
        $requete .= "from demande d join utilisateur u on d.id = u.id ";
        $requete .= "where d.id_1 = :id ";
        $requete .= "and d.accept = 0";
        return $this->conn->query($requete, $param);
    }

     /**
     * suppresion d'une ou plusieurs lignes dans une table
     * @param string $table nom de la table
     * @param array $champs nom et valeur de chaque champs
     * @return true si la suppression a fonctionné
     */	
    public function delete($table, $champs){
        if($this->conn != null){
            // construction de la requête
            $requete = "delete from $table where ";
            foreach ($champs as $key => $value){
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
     * ajout d'une ligne dans une table
     * @param string $table nom de la table
     * @param array $champs nom et valeur de chaque champs de la ligne
     * @return true si l'ajout a fonctionné
     */	
    public function insertOne($table, $champs){
        if($this->conn != null && $champs != null){
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
    public function insertDemande($champs){
        echo "\n" . "coucou insertDemande ";
        if($this->conn != null && $champs != null){
            $requete = "insert into demande (";
            foreach ($champs as $key => $value){
                $requete .= "$key,";
            }
            // (enlève la dernière virgule)
            $requete .= "accept) values (";
            foreach ($champs as $key => $value){
                $requete .= ":$key,";
            }
            $requete .= "0);";
            echo "\n" . $requete;
            return $this->conn->execute($requete, $champs);		
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
    public function updateOne($table, $id, $champs, $numero = null){
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
        if($this->conn != null && $champs != null){
            // construction de la requête
            $requete = "update demande set ";
            $requete .= "accept = 1";				
            $requete .= " where id = :id and id_1 = :id_1;";
            return $this->conn->execute($requete, $champs);		
        }else{
            return null;
        }
    }


}