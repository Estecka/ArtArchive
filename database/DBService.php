<?php
require_once("config.php");
require_once("ArtworkDTO.php");
require_once("TagDTO.php");
require_once("CategoryDTO.php");
require_once("TagListElt.php");

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

	public function StartTransaction(){
		$this->pdo->beginTransaction();
	}
	public function CommitTransaction(){
		$this->pdo->commit();
	}
	public function Rollback(){
		$this->pdo->rollBack();
	}

	/** 
	 * Formats an array to be used into a prepared SQL query. 
	 * In order to prevent SQL injection, make sure the array's keys do not originate from user input; thus it is best used with non-associative arrays.
	 * 
	 * @param array $array 	The array that must be prepared.
	 * @param string $sql 	Outputs the prepared SQL representation of the array. Will be null if $array is empty.
	 * @param array $params	Outputs the array of parameters that must be bound to the prepared query. It will be similar to &array, but with ':' prepended to each key.
	*/
	static private function PrepareSQLArray(array $array, &$sql, &$params) {
		if (sizeof($array) <= 0)
		{
			$params = array();
			$sql = null;
		} 
		else 
		{
			$params = array();
			foreach($array as $key=>$value)
				$params[":$key"] = $value;

			$sql = implode(", ", array_keys($params));
		}
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

	/**
	 * Remove the given tags from the given artwork. 
	 * If `$preserve` is true, it will instead keep the provided tags and remove every other. 
	 * @param string[] $tags 
	*/
	public function RemoveTagsFromArtwork(string $art, array $tags, bool $preserve = false){
		if (!$tags && !$preserve)
			return 0;

		self::PrepareSQLArray($tags, $tags, $params);
		$params[":art"] = $art;
		
		$query = 
		"DELETE FROM `art-tag`
			WHERE artId = (
				SELECT id FROM artworks
				WHERE slug = :art
				LIMIT 1
			)";
		
		if ($tags){
			$IN = $preserve ? "NOT IN" : "IN";
			$query .= 
			"AND tagId IN (
				SELECT id FROM tags
				WHERE slug $IN ($tags)
			)";
		}

		$query = $this->pdo->prepare($query);
		$query->execute($params);
		return $query->rowCount();
	}
	/** @param string[] tags */
	public function AddTagsToArtwork(string $art, array $tags) {
		if (sizeof($tags) <= 0)
			return 0;

		self::PrepareSQLArray($tags, $tags, $params);
		$params[":art"] = $art;
		$tags = $tags ?? ("FALSE");

		$query = $this->pdo->prepare(
			"INSERT IGNORE INTO `art-tag` (tagId, artId)
				SELECT id, (SELECT id FROM `artworks` WHERE slug = :art LIMIT 1)
				FROM `tags` 
				WHERE slug IN ($tags)"
		);
		$query->execute($params);
		return $query->rowCount();
	}
	public function GetTagsFromArtwork(int $artID) : array {
		$query = $this->pdo->prepare(
			"SELECT tags.* FROM tags 
			JOIN `art-tag` ON tags.id = `art-tag`.tagId
			WHERE `art-tag`.`artId` = ?"
		);
		$query->execute(array($artID));
		$result = $query->fetchAll();
		foreach($result as $key => $value)
			$result[$key] = TagDTO::CreateFrom($value);
		return $result;
	}

	/** 
	 * Gets all available tags, along with whether they are assigned to the given Artwork
	 * @return TagListElt[]
	*/
	public function GetArtformTags(int $artID) : array {
		$query = $this->pdo->prepare(
			"SELECT tags.*, art.enabled
			FROM tags 
			LEFT JOIN
				(SELECT tagId, TRUE as enabled
				FROM `art-tag`
				WHERE artId = ?)
				AS art
			ON tags.id = art.tagId
			ORDER BY tags.slug"
		);
		$query->execute(array($artID));
		$result = $query->fetchAll();
		foreach($result as $key=>$value)
			$result[$key] = TagListElt::CreateFrom($value);
		return $result;
	}


	/** REGION TAGS */
	public function GetAllTags() : array {
		$result =  $this->query("SELECT * FROM tags ORDER BY slug");
		foreach($result as $key=>$tag)
			$result[$key] = TagDTO::CreateFrom($tag);
		return $result;
	}
	public function GetTag(string $slug) /*: TagDTO*/ {
		$query = $this->pdo->prepare("SELECT * from tags WHERE slug = ?");
		$query->execute(array($slug));
		$result = $query->fetch();
		$query->closeCursor();

		return $result ? TagDTO::CreateFrom($result) : null;
	}
	public function InsertTag(TagDTO $tag) {
		$query = $this->pdo->prepare("INSERT INTO tags (slug, name, description) VALUES (:slug, :name, :description)");
		$query->execute(array(
			":slug" => $tag->slug,
			":name" => $tag->name,
			":description" => $tag->description,
		));
	}
	public function UpdateTag(string $slug, TagDTO $tag) {
		// Check the tag exists
		$query = $this->pdo->prepare("SELECT COUNT(*) FROM tags WHERE slug = ?");
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

	/** REGION CATEGORIES */
	/**
	 * Fetch a category by its slug.
	 * @return CategoryDTO
	*/
	public function GetCategoryBySlug(string $slug){
		$query = $this->pdo->prepare("SELECT * FROM categories WHERE slug = ?");
		$query->execute(array($slug));
		
		$result = $query->fetch();
		$query->closeCursor();
		return $result ? CategoryDTO::CreateFrom($result) : null;
	}
	public function InsertCategory(CategoryDTO $cat){
		$query = $this->pdo->prepare("INSERT INTO categories (slug, name, description, color) VALUES (:slug, :name, :description, :color)");
		$query->execute(array(
			":slug" => $cat->slug,
			":name" => $cat->name,
			":description" => $cat->description,
			":color" => $cat->color,
		));
	}
	public function UpdateCategory(string $slug, CategoryDTO $cat) : bool {
		// Check the tag exists
		$query = $this->pdo->prepare("SELECT COUNT(*) FROM categories WHERE slug = ?");
		$query->execute(array($slug));

		$count = $query->fetchColumn();
		$query->closeCursor();
		if ($count < 1)
			return false;

		// Perform the change
		$query = $this->pdo->prepare(
			"UPDATE categories 
			SET slug = :newSlug, 
				name = :name, 
				description = :description,
				color = :color
			WHERE slug = :oldSlug"
		);
		$query->execute(array(
			":oldSlug" 	=> $slug,
			":newSlug" 	=> $cat->slug,
			":name" 	=> $cat->name,
			":description" => $cat->description,
			":color" => $cat->color,
		));
		return true;
	}
	public function DeleteCategory(string $slug) : bool {
		$query = $this->pdo->prepare("DELETE FROM categories WHERE slug = ?");
		$query->execute(array($slug));
		return $query->rowCount() > 0;
	}
}
?>