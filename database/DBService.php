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



	/** REGION ARTWORKS */
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

	public function UpdateArtwork(string $slug, ArtworkDTO $art) {
		// Check the artwork exists
		$query = $this->pdo->prepare("SELECT COUNT(*) FROM artworks WHERE slug = ?");
		$query->execute(array($slug));

		$count = $query->fetchColumn();
		if ($count < 1)
			return false;

		// Perform the change
		$query = $this->pdo->prepare(
			"UPDATE artworks SET slug = ?, title = ?, date = ?, description = ? WHERE slug = ?"
		);
		$query->execute(array(
			$art->slug,
			$art->title,
			$art->date,
			$art->description,
			$slug,
		));
		return true;
	}

	public function DeleteArtwork(string $slug) : bool {
		$query = $this->pdo->prepare("DELETE FROM artworks WHERE slug = ?");
		$query->execute(array($slug));
		return $query->rowCount() > 0;
	}


	/** REGION TAGS */
	public function GetTag(string $slug) /*: TagDTO*/ {
		$query = $this->pdo->prepare("SELECT * from tags WHERE slug = ?");
		$query->execute(array($slug));
		$result = $query->fetch();
		$query->closeCursor();

		return $result ? TagDTO::CreateFrom($result) : null;
	}
	public function InsertTag(TagDTO $tag) {
		$query = $this->pdo->prepare("INSERT INTO tags (slug, name, description) VALUES (:slug, :name, description)");
		$query->execute(array(
			":slug" => $tag->slug,
			":name" => $tag->name,
			":description" => $tag->description,
		));
	}
	public function UpdateTag(string $slug, TagDTO $tag) {
		// Check the tag exists
		$query = $this->pdo->prepare("SELECT COUNT(*) FROM tag WHERE slug = ?");
		$query->execute(array($slug));

		$count = $query->fetchColumn();
		$query->closeCursor();
		if ($count < 1)
			return false;

		// Perform the change
		$query = $this->pdo->prepare(
			"UPDATE tags SET slug =:newSlug, name = :name, description = :description WHERE slug = :oldSlug"
		);
		$query->execute(array(
			":oldSlug" 	=> $slug,
			":newSlug" 	=> $tag->slug,
			":name" 	=> $tag->name,
			":description" => $tag->description,
		));
		return true;
	}
	public function DeleteTag(string $slug) : bool {
		$query = $this->pdo->prepare("DELETE FROM tags WHERE slug = ?");
		$query->execute(array($slug));
		return $query->rowCount() > 0;
	}
}
?>