<?php
require("../../ArtArchive.php");
ArtArchive::RequireWebmaster();

if (!empty($_POST)){
	include __ROOT__."/database/Actions/update-tag.php";
	exit;
}

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

$page = new PageBuilder("Edit : $name");
$page->StartPage();
	print("<h2>Edit tag</h2>");
	$page->TagForm($tag, $cats);
$page->EndPage();

?>