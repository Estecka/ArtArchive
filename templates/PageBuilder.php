<?php
require_once(__ROOT__."/database/ArtworkDTO.php");

class PageBuilder{
	public $title = "ArtDump";
	public $charset = "windows-1252";

	public $stylesheets = array(
		"/css/layout.css",
		"/css/colors.css",
	);
	public $rssfeeds = array(
		"All Artworks" => "/feed.xml",
	);

	public function StartPage(){ 
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<title><?= $this->title ?></title>
			<meta charset="<?=$this->charset?>"/>
			<?php
			foreach($this->stylesheets as $uri){
				?>
				<link rel=stylesheet type=text/css href="<?=$uri?>"/>
				<?php
			}
			foreach($this->rssfeeds as $title=>$uri){
				?>
				<link rel=alternate type=application/rss+xml href="<?=$uri?>" title="<?=$title?>"/>
				<?php
			}
			?>
		</head>
		<body>
			<?php
			include("header.php");
	}

	public function EndPage(){
		include ("footer.php");
		?>
		</body>
		</html>
	<?php
	}

	static public function ErrorDocument(int $code, string $message = null){
		http_response_code($code);
		$page = new PageBuilder();
		$page->title = $code;
		$page->StartPage();
			print("<h1>$code</h1>");
			print($message);
		$page->EndPage();
	}

	/**
	 * @param string $urlFormat Url where %d represents the page number. E.g: "http://url?page=%d"
	 * @param int $currentPage Zero-based index of the current page
	 * @param int $pageAmount The total amount of page, from start to end.
	 * @param int $maxRange How many links to nearby pages should be displayed.
	 */
	public function PageList(string $urlFormat, int $currentPage, int $pageAmount, int $maxRange = 11){
		if ($pageAmount > 1)
			include(__ROOT__."/templates/pageList.php");
	}

	/**
	 * @param ArtWorkDTO $art
	 * @param TagDTO[] $tags List of all available tags. Each tag should provide an additional `enabled`  property.
	 * @param CategoryDTO[] $cats List of all available categories.
	 * @param string[] $files The urls this artwork's files.
	 */
	public function ArtForm(ArtworkDTO $art, array $tags, array $cats, array $files, $action = null){
		$page = &$this;
		include(__ROOT__."/templates/artworkForm.php");
	}

	public function ArtCard(ArtworkDTO $art){
		include(__ROOT__."/templates/ArtCard.php");
	}
	/**
	 * @param ArtworkDTO[] $arts
	 */
	public function ArtCardList(array $arts){
		?>
		<div class="cardList">
			<?php
			foreach($arts as $art)
				$this->ArtCard($art);
			?>
		</div>
		<?php
	}


	/**
	 * @param ArtDTO $art The artwork to be displayed
	 * @param TagDTO[] $tags The tags that belong to this artwork.
	 * @param CategoryDTO[] $cats a list of categories, containing at least those represented in the provided tags. (Except for null)
	 * @param string[] $files The url to the files to be displayed
	 */
	public function ArtPage(ArtworkDTO $art, array $tags = null, array $cats = null, array $files = null){
		$page = &$this;
		include(__ROOT__."/templates/artPage.php");
	}
	/**
	 * @param TagDTO $tag The tag to populate the form with
	 * @param CategoryDTO[] $cats A list of all available categories.
	 * @param string $action The action to be taken when submitting the form.
	 */
	public function TagForm(TagDTO $tag, array $cats, $action = null){
		include(__ROOT__."/templates/tagForm.php");
	}

	/**
	 * @param TagDTO[] $tags
	 * @param CategoryDTO[] $cats
	 * @param bool $allowInserts If true, the user will be able to freely enter any tags into the categories of this form.
	 */
	public function TagSelectionForm(array $tags, array $cats, bool $allowInserts){
		include(__ROOT__."/templates/tagSelectionForm.php");
	}

	/**
	 * Presents the provided tags in a nice list, sorted by category.
	 * @param TagDTO[] $tags The tags displayed in the list.
	 * @param CategoryDTO[] $cats a list of categories containing at least those represented in the provided tags. (Except for null)
	 * @param bool $showEmptyCats Whether empty categories should be listed.
	 */
	public function TagList(array $tags, array $cats, bool $showEmptyCats = false) {
		include(__ROOT__."/templates/tagList.php");
	}
	/**
	 * Presents the provided tags in a nice table, sorted by category.
	 * @param TagDTO[] $tags The tags displayed in the list.
	 * @param CategoryDTO[] $cats A list of categories to display, and at at least those represented in the provided tags.
	 */
	public function TagTable(array $tags, array $cats) {
		$page = $this;
		include(__ROOT__."/templates/tagTable.php");
	}

	/**
	 * Display a single category and its tags in a liquid fashion.
	 * @param CategoryDTO $cat
	 * @param TagDTO[] $tags
	 * @param int $rowmax The maximum amount of tags before creating a new block.
	 * @param callable $printCat function(CategoryDTO) => Formats and prints the name of the Category.
	 * @param callable $printTag function(TagDTO) => Formats and prints the name of the tag.
	 */
	public function TagLiquid(CategoryDTO $cat, array $tags, int $rowMax, callable $printCat, callable $printTag){
		include(__ROOT__."/templates/tagLiquid.php");
	}

	public function CategoryForm(CategoryDTO $cat, $action = null){
		include(__ROOT__."/templates/categoryForm.php");
	}

	/** REGION MEDIA */
	public function Media (string $path) {
		$url = URL::Media($path);
		
		$name = $path;
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$type = trim($type); // removes \n. There WILL be new lines

		switch ($type) {
			default :
				include(__ROOT__."/templates/media/default.php");
				break;

			case "jpeg" : 
			case "jpg" : 
			case "png" : 
			case "bmp" : 
			case "gif" :
				include(__ROOT__."/templates/media/image.php"); 
				break;
			
			case "mp3":
			case "wav":
			case "ogg":
			case "m4a":
				include(__ROOT__."/templates/media/audio.php");
				break;
			
			case "txt":
			case "pdf":
			case "html":
			case "htm":
				include(__ROOT__."/templates/media/iframe.php");
				break;

		}
	}
}
?>