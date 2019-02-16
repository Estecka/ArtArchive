<?php
require_once("../database/ArtworkDTO.php");

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
}
?>