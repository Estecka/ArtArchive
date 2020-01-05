<?php
require("../../../ArtArchive.php");
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
	$page->ArtForm($art, $tags, $cats, array());
	
$page->EndPage();
?>