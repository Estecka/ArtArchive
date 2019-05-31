<?php
require("../../../ArtArchive.php");
ArtArchive::RequireWebmaster();

if (!empty($_POST)){
	include __ROOT__."/database/Actions/insert-tag.php";
	exit;
}

$bdd = &ArtArchive::$database;
$cats = $bdd->GetAllCategories();

$page = new PageBuilder();
$page->title = "Submit";
$page->StartPage();

	$tag = TagDTO::CreateFrom($_POST);
	$page->TagForm($tag, $cats);
	
$page->EndPage();
?>