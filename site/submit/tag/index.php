<?php
require("../../../ArtArchive.php");

$bdd = new DBService();
$cats = $bdd->GetAllCategories();

$page = new PageBuilder();
$page->title = "Submit";
$page->StartPage();

	$tag = TagDTO::CreateFrom($_POST);
	$page->TagForm($tag, $cats, "insert.php");
	
$page->EndPage();
?>