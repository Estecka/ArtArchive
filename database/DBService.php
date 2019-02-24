<?php
require_once("config.php");
require_once("ArtworkDTO.php");

class DBService {
	/** @var PDO **/
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
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	public function query($sql, array $params = null)
	{
		if ($params == null){
			$resp = $this->pdo->query($sql);
			return self::preprocess($resp);
		}
		else {
			$query = $this->pdo->prepare($sql);
			$resp = $query->execute($params);
			return self::preprocess($resp);
		}
	}

	static private function preprocess($response){
		$result = array();
		while($row = $response->fetch())
			$result[] = $row;
		$response->closeCursor();
		return $result;
	}



	public function GetArtworks()
	{
		$result =  $this->query("SELECT * FROM artworks ORDER BY date DESC");
		foreach($result as $key=>$art)
			$result[$key] = ArtworkDTO::CreateFrom($art);
		return $result;
	}

	public function GetArtwork($slug)/*: ?ArtworkDTO*/ {
		$query = $this->pdo->prepare("SELECT * FROM artworks WHERE slug = ? LIMIT 1");
		$query->execute(array($slug));
		$result = $query->fetch();
		$query->closeCursor();
		return $result ? ArtworkDTO::CreateFrom($result) : null;
	}

	public function AddArtwork(ArtworkDTO $art){
		$query = $this->pdo->prepare("INSERT INTO artworks (slug, title, date, description) VALUES (?,?,?,?)");
		$result = $query->execute(array(
			$art->slug,
			$art->title,
			$art->date,
			$art->description,
		));
		return $result;
	}

}
?>