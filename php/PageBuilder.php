<?php
require_once(__ROOT__."/database/ArtworkDTO.php");
require_once(__ROOT__."/database/CategoryDTO.php");
require_once(__ROOT__."/database/TagDTO.php");
require_once(__ROOT__."/php/OpenGraphBuilder.php");
require_once(__ROOT__."/php/Url.php");

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
			ArtArchive::$settings['stylesheet']."?masonry=".ArtArchive::$settings['tagMasonry'],
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
			<meta name="robots" content="noai, noimageai">
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
			include(__ROOT__."/templates/header.php");
			?><main><?php
	}

	public function EndPage(){
		?></main><?php
		include (__ROOT__."/templates/footer.php");
		?>
		</body>
		</html>
	<?php
	}


/******************************************************************************/
/* # Error Documents                                                          */
/******************************************************************************/

	static public function ErrorDocumentDebug(int $code, $debugInfo = null){
		return self::ErrorDocument($code, null, null, $debugInfo);
	}

	static public function ErrorDocument(int $code, string $title = null, string $message = null, string $debugInfo = null){
		http_response_code($code);

		if (!empty($title))
			$title = "$code - $title";
		else
			$title = $code;

		$page = new PageBuilder($title);
		$page->StartPage();
		?><article>
			<h1><?=$title?></h1>
			<p><?=$message?></p>

			<?php
			if (ArtArchive::$isWebmaster && !empty($debugInfo)) {
				?>
				<h2>Debug Info :</h2>
				<p><?=$debugInfo?></p>
				<?php
			}
			?>
		</article>
		<?php
		$page->EndPage();
	}


/******************************************************************************/
/* # Misc Widgets                                                             */
/******************************************************************************/

	/**
	 * @param string $urlFormat Url where %d represents the page number. E.g: "http://url?page=%d"
	 * @param int $currentPage Zero-based index of the current page
	 * @param int $pageAmount The total amount of page, from start to end.
	 * @param int $maxRange How many links to nearby pages should be displayed.
	 */
	public function PageList(string $urlFormat, int $currentPage, int $pageAmount, int $maxRange = 10){
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
	 * @param string $links	The list of link, with one link per line.
	 */
	public function	MaskedImage(string $src){
		?><span
			class=maskedImg
			style="--mask:url(<?=$src?>)"
		><img
			src="<?=$src?>"
		/></span><?php
	}


/******************************************************************************/
/* # Artworks                                                                 */
/******************************************************************************/

	public function ArtCard(ArtworkDTO $art){
		include(__ROOT__."/templates/artCard.php");
	}
	/**
	 * TODO: Automatic thumbnail fetching. Optional parameter to manually pass
	 * the thumbnails if ever required.
	 * 
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


/******************************************************************************/
/* # Tags & Categories                                                        */
/******************************************************************************/

	/**
	 * A link to a single tag.
	 * @param $color The color code  or name  for the  category's color. This is
	 * usually defined in the  html parents, in which case it doesn't need to be
	 * defined here.
	 */
	public function TagLink(TagDTO $tag, string $color=null){
		if ($color != null)
			$color = "style='--cat-color:$color'";
		?>
		<a 
			class=tagName
			href="<?=URL::Tag($tag->slug)?>" 
			title="<?=$tag->slug?>"
			<?=$color?>
		><?=$tag->GetName()?></a><?php
	}

	/**
	 * A tag link followed by a short description.
	 * @param $color See TagLink.
	 */
	public function TagPreview(TagDTO $tag, $color=null){
		if ($tag->description != null) {
			$shortDesc = explode("\n", $tag->description, 2);
			if (!empty($shortDesc)){
				$shortDesc = $shortDesc[0];
				$shortDesc = trim($shortDesc);
			}
		}
		?>
		<div class=tagPreview>
			<?php
			$this->TagLink($tag, $color);
			if (!empty($shortDesc)) {
				?><span class=tagShortDesc><?=Markdown::MarkdownToHtml($shortDesc)?></span><?php
			}
			?>
		</div>
		<?php
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
	 * Form used when assigning tags to an artwork.
	 * Also formerly used by the search form.
	 * @param TagDTO[] $tags Each tag is provided with an additional property `enabled`.
	 * @param CategoryDTO[] $cats
	 * @param bool $allowInserts If true, the user will be able to freely enter any tags into the categories of this form.
	 */
	public function TagSelectionForm(array $tags, array $cats, bool $allowInserts){
		$page = $this;
		$showEmptyCats = $allowInserts;
		include(__ROOT__."/templates/tagCheckboxForm.php");
	}

	/**
	 * Form used for searching artworks by various flavors of whitelisted and 
	 * blacklisted tags.
	 * @var TagDTO[] $tags
	 * @var CategoryDTO[] $cats
	 * @var string[] $tagToStatus maps tag slugs to their initial status.
	 */
	public function SearchForm(array $tags, array $cats, array $tagToStatus){
		$page = $this;
		include(__ROOT__."/templates/tagRadioForm.php");
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
	 * Display multiple tags and categories in a liquid fashion.
	 * @var TagDTO[] $tags
	 * @var CategoryDTO[] $cats
	 * @var callable $printTag
	 * @var callable $printCat
	 * @var string $nullCatName
	 * @var bool $showEmptyCats
	 */
	public function LiquidTable(array $tags, array $cats, callable $printCat, callable $printTag, string $nullCatName = "Others", bool $showEmptyCats = false){
		$page = $this;
		include(__ROOT__."/templates/liquidTable.php");
	}

	/**
	 * Display a single category and its tags in a liquid fashion.
	 * @param CategoryDTO $cat
	 * @param TagDTO[] $tags
	 * @param int $rowmax The maximum amount of tags before creating a new block.
	 * @param callable $printCat function(CategoryDTO) => Formats and prints the name of the Category.
	 * @param callable $printTag function(TagDTO) => Formats and prints the name of the tag.
	 */
	public function LiquidCategory(CategoryDTO $cat, array $tags, callable $printCat, callable $printTag){
		include(__ROOT__."/templates/liquidCategory.php");
	}

	public function CategoryForm(CategoryDTO $cat, $action = null){
		include(__ROOT__."/templates/categoryForm.php");
	}


/******************************************************************************/
/* # Media                                                                    */
/******************************************************************************/

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
			
			case EMedia_video:
				include(__ROOT__."/templates/media/video.php");
				break;
			
			case EMedia_iframe:
				include(__ROOT__."/templates/media/iframe.php");
				break;
		}
	}
}
?>