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
	 * @param string $prefix If this value is set paramater's name will be formated as ":prefix_key" instead of ":key". Use this if you need to bind multiple arrays with the same key names.
	*/
	static private function PrepareSQLArray(array $array, &$sql, &$params, string $prefix=null) {
		if (sizeof($array) <= 0)
		{
			$params = array();
			$sql = null;
		} 
		else 
		{
			$params = array();
			foreach($array as $key=>$value){
				$name = $prefix ? ":$prefix"."_$key" : ":$key";
				$params[$name] = $value;
			}

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

	/** REGION FILES */
	/**
	 * Get the URL of all files associated to a given artwork, rooted to the `/storage/` folder
	 * @param int $id The id of the artwork to fetch files from.
	 * @return string[]
	 */
	public function GetFiles(int $id) : array {
		$query = $this->pdo->prepare(
			"SELECT `url` FROM `art-file`
			WHERE `artworkId` = :id
			ORDER BY `order` ASC"
		);
		$query->execute(array(":id" => $id));
		$result = $query->fetchAll();
		return $result;
	}
	/**
	 * Disassociate all files from an artwork.
	 * @param string $slug The artwork's slug
	 */
	public function ClearFiles(string $slug) : void {
		$query = $this->pdo->prepare(
			"DELETE FROM `art-file`
			WHERE artworkId = (
				SELECT id FROM artworks
				WHERE slug = :slug
				LIMIT 1
			)"
		);
		$query->execute(array(":slug" => $slug));
	}
	/**
	 * @param string $slug The artwork's slug
	 * @param string[] $files The urls to the file, starting from below the `/storage/` folder
	 */
	public function AddFiles(string $slug, array $files) : void {
		self::PrepareSQLArray($files, $fileSQL, $params);
		$paramNames = array_keys($params);

		$VALUES = "VALUES\n";
		for ($i=0; $i<sizeof($files); $i++){
			$VALUES .= "(@id, $i, ".$paramNames[$i].")";
		}

		$query = $this->pdo->prepare(
			"SET @id = (SELECT id from `artworks` WHERE slug = :slug LIMIT 1);\n".
			"INSERT INTO `art-file` (artworkId, order, url) $VALUES;"
		);
		$query->execute(array(":slug" => $slug));
	}
	/**
	 * @param string $slug The artwork's slug
	 * @param string[] $files The urls to the file, starting from below the `/storage/` folder
	 */
	public function SetFiles(string $slug, array $files) : void {
		self::PrepareSQLArray($files, $fileSQL, $params);
		$paramNames = array_keys($params);

		$VALUES = "VALUES\n";
		for ($i=0; $i<sizeof($files); $i++){
			$VALUES .= "(@id, $i, ".$paramNames[$i].")";
		}

		$query = $this->pdo->prepare(
			"SET @id = (SELECT id from `artworks` WHERE slug = :slug LIMIT 1);\n".
			"DELETE FROM `art-file`
			WHERE artworkId = @id;\n".
			"INSERT INTO `art-file` (artworkId, order, url) $VALUES;"
		);
		$query->execute(array(":slug" => $slug));
	}

	/** REGION TAGS */
	/**
	 * @return TagDTO[]
	 */
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
		$query = $this->pdo->prepare(
			"INSERT INTO tags (slug, name, description, categoryId) 
			VALUES (:slug, :name, :description, :category)"
		);
		$query->execute(array(
			":slug" => $tag->slug,
			":name" => $tag->name,
			":description" => $tag->description,
			":category" => $tag->categoryId,
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
			"UPDATE tags SET 
				slug =:newSlug, 
				name = :name, 
				description = :description, 
				categoryId = :category 
			WHERE slug = :oldSlug"
		);
		$query->execute(array(
			":oldSlug" 	=> $slug,
			":newSlug" 	=> $tag->slug,
			":name" 	=> $tag->name,
			":description" => $tag->description,
			":category" => $tag->categoryId,
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
	 * Fetch all available categories. Returns an associative array using the categories IDs as keys, and sorted by their `order` property.
	 * @return CategorytDTO[]
	 */
	public function GetAllCategories() : array {
		$result = $this->query("SELECT * FROM categories ORDER BY `order`, `id`");
		$r = array();
		foreach($result as $key=>$cat){
			$cat = CategoryDTO::CreateFrom($cat);
			$r[$cat->id] = $cat;
		}
		return $r;
	}
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
	/**
	 * @param int[] $order An associative array taking categories' slug as keys, and their intended position as value. 
	 */
	public function ReorderCategories(array $order) {
		self::PrepareSQLArray(array_keys($order), $slugs, $slugParam, "slug");
		self::PrepareSQLArray(array_values($order), $orderSQL, $orderParam, "order");
	
		$WHEN_THEN_ = "";
		for ($i=0; $i<sizeof($order); $i++)
			$WHEN_THEN_ .= "WHEN :slug_$i THEN :order_$i\n";

		$query =
			"UPDATE `categories`
			SET `order` = CASE `slug`
				$WHEN_THEN_
				ELSE `order`
				END
			WHERE `slug` IN ($slugs)";

		$query = $this->pdo->prepare($query);
		$query->execute($slugParam + $orderParam);

	}
}
?>