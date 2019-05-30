<?php
require_once(__ROOT__."\database\ArtworkDTO.php");

class PageBuilder{
	public $title = "ArtDump";
	public $charset = "windows-1252";

	public function StartPage(){ 
		?>
		<!DOCTYPE html>
		<html>
		<body>
			<head>
				<title><?= $this->title ?></title>
				<meta charset=" <?=$this->charset?> "/>
			</head>
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
	 * @param int $pageAmount
	 * @param int $maxRange How many links to the nearby pages should be displayed.
	 */
	public function PageList(string $urlFormat, int $currentPage, int $pageAmount, int $maxRange = 11){
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

	public function CategoryForm(CategoryDTO $cat, $action = null){
		include(__ROOT__."/templates/categoryForm.php");
	}
}
?>