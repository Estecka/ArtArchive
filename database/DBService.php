<?php
require("config.php");

class DBService {
	private $pdo;

	public function __construct() 
	{
		$this->pdo = new PDO(
			'mysql:host='.BDDHOST
				.';dbname='.BDDNAME
				.';charset=utf8',
			BDDID, 
			BDDPWD
		);
	}
	
	public function query($sql, array $params = null)
	{
		if ($params == null){
			return $this->pdo->query($sql);
		}
		else {
			$query = $this->pdo->prepare($sql);
			return $query->execute($params);
		}
	}



	public function GetArtworks()
	{
		return $this->query("SELECT * FROM artworks ORDER BY date DESC");
	}

}
?>