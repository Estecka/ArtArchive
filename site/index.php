<?php
require("../Artarchive.php");
$bdd = new DBService();

$pageNo = either($_GET["page"], 0);
$artworks = $bdd->GetArtworks(10, $pageNo, $total);

$page = new PageBuilder();
$page->title = ArtArchive::GetSiteName();
$page->StartPage();

	foreach($artworks as $art)
		$page->ArtCard($art);
	if ($total > 10){
		$pageAmount = (int)ceil($total * 0.1);
		$page->PageList(URL::Home()."?page=%d", $pageNo, $pageAmount, 11);
	}

$page->EndPage();
?>
