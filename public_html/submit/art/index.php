<?php
require("../../../ArtArchive.php");
require_once __ROOT__."/templates/artworkForm.php";
ArtArchive::RequireWebmaster();

if (!empty($_POST)){
	include __ROOT__."/database/Actions/insert-artwork.php";
	exit;
}


$bdd = &ArtArchive::$database;

$tags = $bdd->GetAllTagsByArtwork(-1);
$cats = $bdd->GetAllCategories();

$page = new PageBuilder("Submit Artwork");
$page->StartPage();

	$art = ArtworkDTO::CreateFrom($_POST);
	template_artworkForm($page, $art, $tags, $cats, array());
	
$page->EndPage();
?>