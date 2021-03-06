<?php
require_once(__ROOT__."/database/ArtworkDTO.php");
require_once(__ROOT__."/templates/OpenGraphBuilder.php");

class PageBuilder{
	private $title = "ArtArchive";
	public $charset = "windows-1252";

	/** @var string[] */
	public $stylesheets;
	public $rssfeeds = array(
		"All Artworks" => "/feed.xml",
	);

	/** @var OpenGraphBuilder */
	public $openGraph;

	public function __construct(string $title = NULL)
	{
		$this->title = $title ? $title : ArtArchive::GetSiteName();
		$this->stylesheets = array(
			"/css/stylesheet.css?masonry=".ArtArchive::$settings['tagMasonry'],
		);
		$this->openGraph = new OpenGraphBuilder();
		$this->openGraph->siteName = ArtArchive::GetSiteName();
		$this->openGraph->url = URL::Root().$_SERVER['REQUEST_URI'];
		$this->openGraph->title = $this->title;
	}

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
			$this->openGraph->Flush();
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
		$page = new PageBuilder($code);
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
	 * @param string $links	The list of link, with one link per line.
	 */
	public function	LinkList(string $links){
		include(__ROOT__."/templates/linkList.php");
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
		$page = $this;
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
	public function TagLiquid(CategoryDTO $cat, array $tags, callable $printCat, callable $printTag){
		include(__ROOT__."/templates/tagLiquid.php");
	}

	public function CategoryForm(CategoryDTO $cat, $action = null){
		include(__ROOT__."/templates/categoryForm.php");
	}

	/** REGION MEDIA */
	public function Media (string $path) {
		$url = URL::Media($path);
		$name = $path;

		switch (GetMediaType($path)) {
			default :
			case EMedia_undefined:
				include(__ROOT__."/templates/media/default.php");
				break;

			case EMedia_image :
				include(__ROOT__."/templates/media/image.php"); 
				break;
			
			case EMedia_audio:
				include(__ROOT__."/templates/media/audio.php");
				break;
			
			case EMedia_iframe:
				include(__ROOT__."/templates/media/iframe.php");
				break;
		}
	}
}
?>