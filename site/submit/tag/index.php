<?php
require("../../../ArtArchive.php");
ArtArchive::RequireWebmaster();

$bdd = &ArtArchive::$database;
$cats = $bdd->GetAllCategories();

$page = new PageBuilder();
$page->title = "Submit";
$page->StartPage();

	$tag = TagDTO::CreateFrom($_POST);
	$page->TagForm($tag, $cats, "insert.php");
	
$page->EndPage();
?>