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
	 * @param ArtWorkDTO $art
	 * @param TagListelt[] $tags
	 */
	public function ArtForm(ArtworkDTO $art, array $tags, $action = null){
		include(__ROOT__."/templates/artworkForm.php");
	}

	public function ArtCard(ArtworkDTO $art){
		include(__ROOT__."/templates/ArtCard.php");
	}

	public function ArtPage(ArtworkDTO $art, array $tags = null){
		include(__ROOT__."/templates/artPage.php");
	}
	public function TagForm(TagDTO $tag, $action = null){
		include(__ROOT__."/templates/tagForm.php");
	}

	public function CategoryForm(CategoryDTO $cat, $action = null){
		include(__ROOT__."/templates/categoryForm.php");
	}
}
?>