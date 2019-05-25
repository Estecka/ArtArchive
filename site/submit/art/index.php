<?php
require("../../../ArtArchive.php");
ArtArchive::RequireWebmaster();

$bdd = &ArtArchive::$database;

$tags = $bdd->GetAllTagsByArtwork(-1);
$cats = $bdd->GetAllCategories();

$page = new PageBuilder();
$page->title = "Submit";
$page->StartPage();

	$art = ArtworkDTO::CreateFrom($_POST);
	$page->ArtForm($art, $tags, $cats, array(), "insert.php");
	
$page->EndPage();
?>