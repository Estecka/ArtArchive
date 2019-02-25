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

	public function ArtForm(ArtworkDTO $art, $action = null){
		include(__ROOT__."/templates/artworkForm.php");
	}

	public function ArtCard(ArtworkDTO $art){
		include(__ROOT__."/templates/ArtCard.php");
	}

	public function ArtPage(ArtworkDTO $art){
		include(__ROOT__."/templates/artPage.php");
	}
}
?>