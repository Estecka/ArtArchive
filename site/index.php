<?php
require("../Artarchive.php");
$bdd = &ArtArchive::$database;

$pageNo = either($_GET["page"], 0);
$artworks = $bdd->GetArtworks(10, $pageNo, $total);
$artworks = $bdd->GetThumbnails($artworks);

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
