<?php
require("../ArtArchive.php");
$bdd = &ArtArchive::$database;

$pageNo = either($_GET["page"], 0);
$artworks = $bdd->GetArtworks(10, $pageNo, $total);
if ($artworks)
	$artworks = $bdd->GetThumbnails($artworks);

$page = new PageBuilder();
$page->title = ArtArchive::GetSiteName();
$page->StartPage();

	$page->ArtCardList($artworks);
	if ($total > 10){
		$pageAmount = (int)ceil($total * 0.1);
		$page->PageList(URL::Home()."?page=%d", $pageNo, $pageAmount, 11);
	}

$page->EndPage();
?>
