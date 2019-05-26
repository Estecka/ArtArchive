<?php
require_once("config.php");
require_once("ArtworkDTO.php");
require_once("TagDTO.php");
require_once("CategoryDTO.php");
require_once("TagListElt.php");

class DBService {
	static public $version = 1;

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



	/** REGION SITE */
	/**
	 * @param string[] $settings An associative array with setting names as key, filled with default values.
	 * @return string[] Array of setting values with setting names as key.
	 */
	public function GetSettings(array $settings = null) : array {
		if ($settings == null)
		{
			$query = $this->pdo->query("SELECT * FROM `settings`;");
			$settings = $query->fetchAll();
		}
		else 
		{
			self::PrepareSQLArray(array_keys($settings), $names, $params);
			$query = $this->pdo->prepare("SELECT `name`, `value` FROM `settings` WHERE `name` IN ($names);");
			$query->execute($params);
			$result = $query->fetchAll();
			foreach($result as $entry)
				$settings[$entry['name']] = $entry['value'];
		}

		return $settings;
	}
	/**
	 * @param mixed[] $settings An associative array of setting values with setting names as key.
	 */
	public function SetSettings(array $settings) {
		if (sizeof($settings) <= 0)
			return;

		$names  = array_keys  ($settings);
		$values = array_values($settings);
		self::PrepareSQLArray($names,  $namesql, $nameParams,  "key"  );
		self::PrepareSQLArray($values, $valusql, $valueParams, "value");

		$params = array_merge($nameParams, $valueParams);
		$nameParams  = array_keys($nameParams );
		$valueParams = array_keys($valueParams);

		$VALUES = array();
		for ($i=0; $i < sizeof($valueParams); $i++) {
			$key   = $nameParams [$i];
			$value = $valueParams[$i];
			$VALUES []= "($key, $value)";
		}
		$VALUES = "VALUES \n".implode(", \n", $VALUES);

		$query = 
			"INSERT INTO `settings` (`name`, `value`) $VALUES 
			ON DUPLICATE KEY UPDATE 
				`value` = VALUES (`value`)";

		$query = $this->pdo->prepare($query);
		$query->execute($params);
	}

	public function GetPage(string $name) : string {
		$query = $this->pdo->prepare("SELECT `value` FROM `pages` WHERE `name` = ? LIMIT 1;");
		$query->execute(array($name));

		return $query->fetchColumn();
	}
	public function SetPage(string $name, string $value) : bool {
		$query = $this->pdo->prepare("INSERT INTO `pages` VALUES (:n, :v) ON DUPLICATE KEY UPDATE `value` = :v;");
		$r = $query->execute(array(
			':n' => $name,
			':v' => $value,
		));

		return (bool)$r;
	}


	/** REGION ARTWORKS */
	public function GetArtworks(int $amount, int $page, int &$total = null)
	{
		$total = $this->pdo->query("SELECT count(id) FROM artworks;")->fetchColumn();

		$query = $this->pdo->prepare("SELECT * FROM artworks ORDER BY date DESC LIMIT :offset, :amount");
		$query->bindValue(":offset", $amount*$page, PDO::PARAM_INT);
		$query->bindValue(":amount", $amount,       PDO::PARAM_INT);
		$query->execute();
		$result = $query->fetchAll();

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
	/**
	 * Seeks artworks that are assigned all of the provided tags.
	 * @param int[] $tags The Id of the required tags
	 * @param int $amount
	 * @param int $page
	 * @param int $total Outputs the total number of results.
	 * @return ArtworkDTO[]
	 */
	public function GetArtworksByTags(array $tags, int $amount, int $page, int &$total = null){
		
		// #1 Save all matching artworks id into a temporary table
		self::PrepareSQLArray($tags, $sql, $params);
		$paramNames = array_keys($params);

		$INNER_JOIN_tags = "\n";
		for ($i=0; $i<sizeof($tags); $i++){
			$tag = $paramNames[$i];
			$INNER_JOIN_tags .= 
				"INNER JOIN \n"
				."	(SELECT artId as id FROM `art-tag` WHERE tagId = $tag) as `arts$i` \n"
				."	ON `arts$i`.id = `artworks`.id \n";
		}

		$query = 
			"CREATE TEMPORARY TABLE `foundArts` \n"
			."SELECT `artworks`.id FROM `artworks` \n"
			."$INNER_JOIN_tags;";
		$query = $this->pdo->prepare($query);
		$query->execute($params);

		// #2 Count them.
		$total = $this->pdo->query("SELECT count(id) FROM `foundArts`")->fetchColumn();

		// #3 Return a limited set of result.
		$query = $this->pdo->prepare(
			"SELECT `artworks`.* FROM `artworks` 
			INNER JOIN `foundArts` ON `foundArts`.id = `artworks`.id
			LIMIT :offset, :amount;"
		);
		$query->bindValue(":offset", $amount*$page, PDO::PARAM_INT);
		$query->bindValue(":amount", $amount,       PDO::PARAM_INT);
		$query->execute();

		$result = $query->fetchAll();
		foreach($result as $key=>$art)
			$result[$key] = ArtworkDTO::CreateFrom($art);
		return $result;
	}

	public function AddArtwork(ArtworkDTO $art) : bool {
		$query = $this->pdo->prepare("INSERT INTO artworks (slug, title, date, description) VALUES (?,?,?,?)");
		$result = $query->execute(array(
			$art->slug,
			$art->title,
			$art->date,
			$art->description,
		));
		return $result;
	}

	/**
	 * @param string $slug
	 * @param ArtworkDTO $art
	 * @return bool false if the Artwork doesn't exist.
	 */
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
	/** 
	 * @param string $art The slug of the arwork
	 * @param string[] tags An array of the tags' slugs
	 * @return int The amount of tags succesfully added. Failures may be due to tags already existing for this artworks.
	 */
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
	/** 
	 * Gets all available tags, along with whether they are assigned to the given Artwork. 
	 * Each tagDTO object is assigned an additional `enabled` property telling whether they are
	 * @param int $artID
	 * @return TagDTO[]
	*/
	public function GetAllTagsByArtwork(int $artID) : array {
		$query = $this->pdo->prepare(
			"SELECT tags.*, assigned.enabled FROM tags
			LEFT JOIN 
				(SELECT tagId, TRUE as `enabled` FROM `art-tag`
				WHERE artId = ?) 
				as `assigned`
			ON tags.id = assigned.tagId
			ORDER BY tags.slug ASC;"
		);
		$query->execute(array($artID));
		$result = $query->fetchAll();
		foreach($result as $key=>$value){
			$result[$key] = TagDTO::CreateFrom($value);
			$result[$key]->enabled = (bool)$value["enabled"];
		}
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
			WHERE `artworkId` = ?
			ORDER BY `order` ASC"
		);
		$query->execute(array($id));
		$result = $query->fetchAll(PDO::FETCH_COLUMN);
		return $result;
	}
	/**
	 * Disassociate all files from an artwork.
	 * @param string $slug The artwork's slug
	 */
	public function ClearFiles(string $slug) {
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
	public function AddFiles(string $slug, array $files) {
		self::PrepareSQLArray($files, $fileSQL, $params);
		$paramNames = array_keys($params);

		$VALUES = array();
		for ($i=0; $i<sizeof($files); $i++){
			$VALUES[] = "\t(@id, $i, ".$paramNames[$i].")";
		}
		$VALUES = implode(", \n", $VALUES);
		$VALUES = "VALUES\n".$VALUES;

		$query = $this->pdo->prepare(
			"SET @id = (SELECT id from `artworks` WHERE slug = :slug LIMIT 1);\n".
			"INSERT INTO `art-file` (`artworkId`, `order`, `url`) $VALUES;"
		);
		$params[":slug"] = $slug;
		$query->execute($params);
	}
	/**
	 * @param string $slug The artwork's slug
	 * @param string[] $files The urls to the file, starting from below the `/storage/` folder
	 */
	public function SetFiles(string $slug, array $files) {
		self::PrepareSQLArray($files, $fileSQL, $params);
		$paramNames = array_keys($params);

		$VALUES = array();
		for ($i=0; $i<sizeof($files); $i++){
			$VALUES[] = "\t(@id, $i, ".$paramNames[$i].")";
		}
		$VALUES = implode(", \n", $VALUES);
		$VALUES = "VALUES\n".$VALUES;

		$query = $this->pdo->prepare(
			"SET @id = (SELECT id from `artworks` WHERE slug = :slug LIMIT 1);\n".
			"DELETE FROM `art-file`
			WHERE artworkId = @id;\n".
			"INSERT INTO `art-file` (`artworkId`, `order`, `url`) \n$VALUES;"
		);
		$params[":slug"] = $slug;
		$query->execute($params);
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
	/**
	 * Returns the Id of the given tags.
	 * @param string[] $tags
	 * @return int[] An associative array wit slugs as key and ids as value. Non-existing tags will still have an entry with `null` as value.
	 */
	public function TagSlugsToID(array $tags) : array {
		self::PrepareSQLArray($tags, $tagsSQL, $params);

		$query = "SELECT id, slug FROM tags WHERE slug IN ($tagsSQL);";
		$query = $this->pdo->prepare($query);
		$query->execute($params);

		$response = $query->fetchAll();

		$result = array();
		foreach($tags as $tag)
			$result[strtolower($tag)] = null;
		foreach($response as $tag)
			$result[strtolower($tag["slug"])] = $tag["id"];

		return $result;
	}
	/**
	 * Inserts a bulk of tags into a given category.
	 * @param string $category The slug of the category
	 * @param string[] $tags The slugs of the tags to insert. Already existing slugs will be ignored.
	 */
	public function InsertTagsBulk(string $category, array $tags) {
		self::PrepareSQLArray($tags, $sql, $params);

		$VALUES = array();
		foreach($params as $key=>$value){
			$VALUES[] = "($key, @id)";
		}
		$VALUES = implode(", \n", $VALUES);
		$VALUES = "VALUES\n".$VALUES;

		$query = 
			"SET @id = (SELECT id FROM `categories` WHERE slug=:category LIMIT 1);\n".
			"INSERT IGNORE INTO `tags` (slug, categoryId) $VALUES;";
		$query = $this->pdo->prepare($query);
		$params[":category"] = $category;
		$query->execute($params);
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