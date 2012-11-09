<?php
/*
## FILE : DB.class.php
DB manager.
*/

class DB{

	private $db;
	private $prefix;

	public function __construct($params){
		$options = array(	PDO::MYSQL_ATTR_INIT_COMMAND    => "SET NAMES utf8" ,
							PDO::ATTR_PERSISTENT => $params->persitent
						);

		try{
			$this->db = new PDO('mysql:host='.$params->host.';dbname='.$params->base, $params->user, $params->password, $options);
		}catch(Exception $e){
			throw new Exception("DB connection error : ".$e->getMessage()." (error ".$e->getCode().")", 1, $e);
		}	

		$this->prefix = $params->prefix;	

		$count = $this->query('SELECT * FROM '.$this->prefix.'blocks');

		if($count == array()){
			throw new Exception("Not installed or bad prefix", 1);
		}

	}

	private function query($queryString, $params = array()){
		try{
			$query = $this->db->prepare($queryString);
			foreach ($params as $key => $value) {
				$query->bindParam(':'.$key, $value, $this->PDOTypeOf($value));
			}
			$query->execute();

			$results = $query->fetchAll(PDO::FETCH_ASSOC);

			$query->closeCursor();

			return $results;
		}catch(Exception $e){
			throw new Exception("Query error : ".$e->getMessage, 1, $e);
			return null;
		}
	}

	private function PDOTypeOf($var){
		if(is_int($var))
			$param = PDO::PARAM_INT;
		elseif(is_bool($var))
			$param = PDO::PARAM_BOOL;
		elseif(is_null($var))
			$param = PDO::PARAM_NULL;
		elseif(is_string($var))
			$param = PDO::PARAM_STR;
		else
			$param = PDO::PARAM_STR;	
		return $param;
	}

}
?>