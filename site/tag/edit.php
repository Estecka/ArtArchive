<?php
require("../../ArtArchive.php");

$slug = value($_GET['tag']);

if (empty($slug)){
	PageBuilder::ErrorDocument(400);
	die;
}
$bdd = &ArtArchive::$database;
$tag = $bdd->GetTag($slug);
$cats = $bdd->GetAllCategories();
	
if ($tag == null){
	PageBuilder::ErrorDocument(404);
	die;
}

$name = $tag->GetName();

$page = new PageBuilder();
$page->title = "Edit : $name";
$page->StartPage();
	print("<h2>Edit tag</h2>");
	$page->TagForm($tag, $cats, "update.php?tag=$slug");
$page->EndPage();

?>