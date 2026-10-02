<?php
require_once("config.php");
require_once("ArtworkDTO.php");
require_once("TagDTO.php");
require_once("CategoryDTO.php");
require_once("TagListElt.php");

class DBService {
	/** 
	 * @var int The expected version for the database's structure.
	 * This may vary from the actual database's version if it's not up to date.
	 */
	static public $version = 3;

	/** @var PDO **/
	public $pdo;

	public function __construct() 
	{
		try {
			$this->pdo = new PDO(
				'mysql:host='.BDDHOST
					.';dbname='.BDDNAME
					.';charset=utf8',
				BDDID, 
				BDDPWD
			);
		} catch (PDOException $e) {
			http_response_code(500);
			echo "Couldn't connect to the database : <br/>".$e->getMessage();
			die;
		}
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
	 * Formats an array to be used into a prepared  SQL query.
	 * Array keys are ignored, parameters will be named after their position in 
	 * the array.
	 * 
	 * @param array $array The array that must be prepared.
	 * @param string $sql Outputs the prepared  SQL representation of the array.
	 * Parenthesis are not included. Will be null if $array is empty.
	 * @param array $params Outputs the array  of parameters  that must be bound
	 * to the  prepared  query. It  will be  similar  to  &array,  but with  ':'
	 * prepended to each key. The existing array will be overwritten.
	 * @param string $prefix Parameters names will be prefixed  with this value.
	 * Use this if you need to bind multiple arrays to the same query.
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
			foreach (array_values($array) as $i=>$value){
				$name = $prefix ? ":$prefix"."_$i" : ":$i";
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

	static public function CheckSlug(string $slug, bool $throw) : bool {
		$r = preg_match("#^[A-Za-z0-9_\-]+$#", $slug);
		if (!$r && $throw)
			throw new PDOException("slug contains unauthorized caracters", 4500);
		else
			return $r;
	}

	/**
	 * Checks the version of the installed database structure version, not to be mistaken with the database php software version.
	 * Can be safely used to check whether the database has been set up.
	 * @return float `false` if no version was found or the query failed. The version number otherwise.
	 */
	public function GetVersion(){
		try {
			return (float)$this->pdo->query("SELECT `value` FROM `settings` WHERE `name`='dbVersion';")->fetchColumn();
		} catch (PDOException $e) {
			return false;
		}
	}


/******************************************************************************/
/* # Site Infos                                                               */
/******************************************************************************/

	/***
	 * Large configs are stored in the page table for the time being.
	 */

	public function GetSettings(array $settings = null) : array {
		return $this->GetSettingByTable('settings', $settings);
	}
	public function GetConfigs(array $settings = null) : array {
		return $this->GetSettingByTable('pages', $settings);
	}
	public function GetPages(array $settings = null) : array {
		return $this->GetSettingByTable('pages', $settings);
	}
	/**
	 * @param string[] $settings An associative array with setting names as key, filled with default values.
	 * @return string[] Array of setting values with setting names as key.
	 */
	private function GetSettingByTable(string $tableName, array $settings = null) : array {
		if ($settings == null)
		{
			$query = $this->pdo->query("SELECT `name`, `value` FROM `$tableName`;");
		}
		else 
		{
			self::PrepareSQLArray(array_keys($settings), $names, $params);
			$query = $this->pdo->prepare("SELECT `name`, `value` FROM `$tableName` WHERE `name` IN ($names);");
			$query->execute($params);
		}

		$result = $query->fetchAll();
		foreach($result as $entry)
			$settings[$entry['name']] = $entry['value'];

		return $settings;
	}
	public function SetSettings(array $settings) {
		return $this->SetSettingByTable('settings', $settings);
	}
	public function SetConfigs(array $settings) {
		return $this->SetSettingByTable('pages', $settings);
	}
	public function SetPages(array $settings) {
		return $this->SetSettingByTable('pages', $settings);
	}
	/**
	 * @param mixed[] $settings An associative array of setting values with setting names as key.
	 */
	private function SetSettingByTable(string $tableName, array $settings) {
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
			"INSERT INTO `$tableName` (`name`, `value`) $VALUES 
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


/******************************************************************************/
/* # Artworks                                                                 */
/******************************************************************************/

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

	public function GetArtworksBySlug(array $slugs)
	{
		self::PrepareSQLArray($slugs, $slugSQL, $slugValues);
		$query = $this->pdo->prepare("SELECT * FROM artworks WHERE slug IN ($slugSQL);");
		$query->execute($slugValues);
		$result = $query->fetchAll();

		$mappedResult = array();
		foreach($result as $key=>$art){
			$art = ArtworkDTO::CreateFrom($art);
			$mappedResult[$art->slug] = $art;
		}
		return $mappedResult;
	}

	public function GetArtwork($slug) : ?ArtworkDTO {
		$query = $this->pdo->prepare("SELECT * FROM artworks WHERE slug = ? LIMIT 1");
		$query->execute(array($slug));
		$result = $query->fetch();
		$query->closeCursor();
		return $result ? ArtworkDTO::CreateFrom($result) : null;
	}

	public function GetArtworksByTagId(int $tagId, int $amount, int $page, int &$total = null) {
		return $this->SearchArtworks(array($tagId), array(), array(), $amount, $page, $total);
	}

	/**
	 * Search artworks by various flavours of whitelists and blackists.
	 * 
	 * @param int[] $required A list of tag IDs. Artworks must have ALL  of these tags.
	 * @param int[] $excluded A list of tag IDs. Artworks must have NONE of these tags.
	 * @param int[][] $included Multiple lists of tag IDs. Artworks must have at least ONE tag FROM EACH list.
	 * @param int $amount The maximum amount of result to return
	 * @param int $page
	 * @param int $total Outputs the total number of results.
	 * @return ArtworkDTO[]
	 */
	public function SearchArtworks(array $required, array $excluded, array $includeGroups, int $amount, int $page, int &$total = null){
		// Generate the SQL query and its parameters.
		// Main SQL is ready to be queried. Sub SQL is ready to be included into
		// another SQL's "where" statement
		$params = array();
		$SQL_MAIN = null;
		$AND_SUBSQL = "";

		if (!empty($excluded)) {
			self::PrepareSQLArray(array_unique($excluded), $TAGLIST_SQL, $localParams, 'exclude_');
			$negative_SQL = "SELECT DISTINCT(artId) FROM `art-tag` WHERE tagId IN ($TAGLIST_SQL)";
			$SQL_MAIN  ="SELECT id FROM artworks WHERE id NOT IN ($negative_SQL)";
			$AND_SUBSQL = "AND artId NOT IN ($negative_SQL)";
			$params = array_merge($params, $localParams);
		}

		foreach(array_values($includeGroups) as $grpIndex=>$included)
		if (!empty($included)) {
			self::PrepareSQLArray(array_unique($included), $TAGLIST_SQL, $localParams, 'include'.$grpIndex.'_');
			$SQL_MAIN = "SELECT DISTINCT(artId) FROM `art-tag` WHERE tagId IN ($TAGLIST_SQL) $AND_SUBSQL";
			$AND_SUBSQL = "AND artId IN ($SQL_MAIN)";
			$params = array_merge($params, $localParams);
		}

		if (!empty($required)) {
			self::PrepareSQLArray(array_unique($required), $TAGLIST_SQL, $localParams, "require_");
			$SQL_MAIN = 
				"SELECT DISTINCT(artId), COUNT(tagId) as score FROM `art-tag` 
					WHERE tagId IN ($TAGLIST_SQL)
					$AND_SUBSQL
				GROUP BY artId
				HAVING score = :score"
				;

			$params = array_merge($params, $localParams);
			$params[':score'] = sizeof($required);
		}

		unset ($AND_SUBSQL);
		if (empty($SQL_MAIN)){
			throw new InvalidArgumentException("Searched artwork with no parameters.");
		}


		// Get the final list of artwork IDs
		$query = $this->pdo->prepare($SQL_MAIN);
		$query->execute($params);
		$results = $query->fetchAll(PDO::FETCH_COLUMN);
		$query->closeCursor();

		$total = sizeof($results);

		if (sizeof($results) <= 0)
			return array();

		// Get the artworks
		self::PrepareSQLArray($results, $RESULTS, $resultParams);
		$query = 
			"SELECT * FROM `artworks` 
			WHERE id IN ($RESULTS)
			ORDER BY `date` DESC, `id` DESC
			LIMIT :offset, :amount;"
			;

		$query = $this->pdo->prepare($query);
		foreach($resultParams as $key=>$value)
				$query->bindValue($key,  $value, PDO::PARAM_INT);
		$query->bindValue(":offset", $amount*$page, PDO::PARAM_INT);
		$query->bindValue(":amount", $amount,       PDO::PARAM_INT);
		$query->execute();

		$result = $query->fetchAll();
		foreach($result as $key=>$art)
			$result[$key] = ArtworkDTO::CreateFrom($art);
		return $result;

	}

	public function AddArtwork(ArtworkDTO $art) : bool {
		self::CheckSlug($art->slug, true);
		$query = $this->pdo->prepare("INSERT INTO artworks
			(slug, title, date, description, thumbUrl, thumbFocusX, thumbFocusY, links)
			VALUES (?,?,?,?,?,?,?,?)"
		);
		$result = $query->execute(array(
			$art->slug,
			$art->title,
			$art->date,
			$art->description,
			$art->thumbUrl,
			$art->thumbFocusX,
			$art->thumbFocusY,
			$art->links,
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
		self::CheckSlug($art->slug, true);
		$query = $this->pdo->prepare("UPDATE artworks
			SET slug = ?, title = ?, date = ?, description = ?, thumbUrl = ?, thumbFocusX = ?, thumbFocusY = ?, links = ?
			WHERE slug = ?"
		);
		$query->execute(array(
			$art->slug,
			$art->title,
			$art->date,
			$art->description,
			$art->thumbUrl,
			$art->thumbFocusX,
			$art->thumbFocusY,
			$art->links,
			$slug,
		));
		return true;
	}

	public function DeleteArtwork(string $slug) : bool {
		$query = "SELECT id FROM artworks WHERE slug = ?";
		$query = $this->pdo->prepare($query);
		$query->execute(array($slug));
		if (!$query->rowCount())
			return false;

		$id = (int)$query->fetchColumn();
		$query->closeCursor();

		$this->DissociateArtwork($id);

		$query = $this->pdo->exec("DELETE FROM artworks WHERE id = $id");
		return true;
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
			WHERE `art-tag`.`artId` = ?
			ORDER BY tags.slug ASC"
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
		// Get rid of carriage returns that slipped into the database through older versions.
		foreach($result as $i=>$path) 
			$result[$i] = trim($path);
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

	/**
	 * Attach to the provided artworks a link to their thumbnails. 
	 * Artworks will be returned with an additional `thumbnail` property.
	 * @var ArtworkDTO[] $artworks
	 * @return ArtworkDTO[]
	 */
	public function GetThumbnails(array $artworks) : array {
		$extensions = array(
			"bmp",
			"jpg",
			"jpeg",
			"png",
			"gif",
			"webp",
		);

		$artIds = array();
		foreach($artworks as $art)
			if (empty($art->thumbUrl))
				$artIds[] = $art->id;
		
		$thumbs = $this->GetMainFiles($artIds, $extensions);
		foreach($artworks as $art)
			if (empty($art->thumbUrl))
				$art->thumbUrl = value($thumbs[$art->id]);

		return $artworks;
	}

	/**
	 * For each artworks, return the first file to meet any of the given extensions.
	 * @param int[] $artIds
	 * @param string[] $extensions
	 * @return string[] Associative array that associates artwork Ids with file pathes.
	 */
	public function GetMainFiles(array $artIds, array $extensions) : array {
		// Shortcircuit error 500 when no thumbnail in a set is the main file.
		if (empty($artIds))
			return array();

		// [[:space:]] is the metacaracter \s (whitespace) for sql regex
		// \\\\. <- escaped caracters needs to be escaped twice in sql regex
		$extensions = implode("|", $extensions);
		$regex = "\\.($extensions)[[:space:]]*$";

		self::PrepareSQLArray($artIds, $artSQL, $params);
		$params[':regex'] = $regex;
		
		$query = 
			"SELECT artworkId, MIN(`order`) as `order` FROM `art-file`
			WHERE artworkID IN ($artSQL)
				AND url REGEXP :regex
			GROUP BY artworkId"
			;
		$query = 
			"SELECT ids.*, files.url 
				FROM ($query) as ids
			INNER JOIN `art-file` as files
				ON ids.artworkId = files.artworkId
				AND ids.`order` = files.`order`"
			;

		$query = $this->pdo->prepare($query);
		$query->execute($params);

		$result = $query->fetchAll();
		$return = array();
		foreach($result as $entry)
			$return[$entry['artworkId']] = $entry['url'];
		
		return $return;
	}


/******************************************************************************/
/* # Tags                                                                     */
/******************************************************************************/

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
		foreach($tags as $tag)
			self::CheckSlug($tag, true);

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
		self::CheckSlug($tag->slug, true);
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
		self::CheckSlug($tag->slug, true);
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
		$query = "SELECT id FROM tags WHERE slug = ? LIMIT 1";
		$query = $this->pdo->prepare($query);
		$query->execute(array($slug));
		if (!$query->rowCount())
			return false;

		$id = (int)$query->fetchColumn();
		$query->closeCursor();

		$this->DissociateTag($id);

		$query = "DELETE FROM tags WHERE id = $id";
		$query = $this->pdo->exec($query);

		return true;
	}


/******************************************************************************/
/* # Categories                                                               */
/******************************************************************************/

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
	public function GetCategoryBySlug(string $slug) : CategoryDTO {
		$query = $this->pdo->prepare("SELECT * FROM categories WHERE slug = ?");
		$query->execute(array($slug));
		
		$result = $query->fetch();
		$query->closeCursor();
		return $result ? CategoryDTO::CreateFrom($result) : null;
	}

	/**
	 * @return CategoryDTO
	*/
	public function GetCategoryById(int $id) : CategoryDTO {
		$query = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
		$query->execute(array($id));
		
		$result = $query->fetch();
		$query->closeCursor();
		return $result ? CategoryDTO::CreateFrom($result) : null;
	}

	public function GetTagsFromCategory(int $catId) : array {
		$query = $this->pdo->prepare("SELECT * FROM tags WHERE categoryId = ? ORDER BY slug ASC");
		$query->execute(array($catId));
		$result = $query->fetchAll();
		foreach($result as $key => $value)
			$result[$key] = TagDTO::CreateFrom($value);
		return $result;
	}

	public function InsertCategory(CategoryDTO $cat){
		self::CheckSlug($cat->slug, true);
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
		self::CheckSlug($cat->slug, true);
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
		$query = "SELECT id FROM categories WHERE slug = ? LIMIT 1";
		$query = $this->pdo->prepare($query);
		$query->execute(array($slug));
		if (!$query->rowCount())
			return false;


		$id = (int)$query->fetchColumn();
		$query->closeCursor();

		$this->DissociateCategory($id);

		$query = $this->pdo->exec("DELETE FROM categories WHERE id = $id");

		return true;
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


/******************************************************************************/
/* # Cleaning                                                                 */
/******************************************************************************/

	/**
	 * Dissociate all tags from the given category.
	 * @var int $id The id of the category to clean.
	 * @return int The number of affected tags.
	 */
	public function DissociateCategory(int $id) : int {
		$query = "UPDATE `tags` SET categoryId = NULL WHERE categoryId = $id";
		$count = $this->pdo->exec($query);
		return $count;
	}

	/**
	 * Dissociate all tags and all files from the given artwork.
	 * @var int $id The id of the artwork to clean
	 * @return int The number of affected tags and files;
	 */
	public function DissociateArtwork(int $id) : int {
		$query = "DELETE FROM `art-tag` WHERE artId = $id";
		$count = $this->pdo->exec($query);

		$query = "DELETE FROM `art-file` WHERE artworkId = $id";
		$count += $this->pdo->exec($query);

		return $count;
	}

	/**
	 * Dissociate all artworks from the given tag.
	 * @var int $id the Id of the tag to dissociate.
	 * @return int The number of affected artworks.
	 */
	public function DissociateTag(int $id) : int {
		$query = "DELETE FROM `art-tag` WHERE tagId = $id";
		$count = $this->pdo->exec($query);
		return $count;
	}

	/**
	 * Find deleted tags that are still assigned to artworks.
	 * @return int[]
	 */
	public function FindOrphanedTags() : array {
		$query = 
			"SELECT DISTINCT(tagId) as tagId FROM `art-tag`
			LEFT JOIN `tags` ON `tags`.`id` = `art-tag`.`tagId`
			WHERE `tags`.`id` IS NULL";

		$query = $this->pdo->query($query);
		$ids = $query->fetchAll(PDO::FETCH_COLUMN);
		return $ids;
	}
	/**
	 * Find deleted artworks that are still assigned to tags.
	 * @return int[]
	 */
	public function FindOrphanedArtworks() : array {
		$query = 
			"SELECT DISTINCT(artId) as artId FROM `art-tag`
			LEFT JOIN `artworks` ON `artworks`.`id` = `art-tag`.`artId`
			WHERE `artworks`.`id` IS NULL";

		$query = $this->pdo->query($query);
		$ids = $query->fetchAll(PDO::FETCH_COLUMN);
		return $ids;
	}
	/**
	 * Find deleted categories that are still assigned to tags.
	 * @return int[]
	 */
	public function FindOrphanedCategories() : array {
		$query = 
			"SELECT DISTINCT(categoryId) as categoryId FROM tags
			LEFT JOIN categories ON tags.categoryId = categories.id
			WHERE categories.id IS NULL";
			
		$query = $this->pdo->query($query);
		$ids = $query->fetchAll(PDO::FETCH_COLUMN);
		return $ids;
	}
	/**
	 * find deleted artworks that are still assigned to files.
	 * @return int[]
	 */
	public function FindOrphanedFiles() : array {
		$query = 
			"SELECT DISTINCT(artworkId) as artId FROM `art-file`
			LEFT JOIN artworks ON `art-file`.artworkId = artworks.id
			WHERE artworks.id IS NULL";
			
		$query = $this->pdo->query($query);
		$ids = $query->fetchAll(PDO::FETCH_COLUMN);
		return $ids;
	}
	
}
?>
