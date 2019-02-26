<?php
require("../../ArtArchive.php");

$slug = value($_GET['tag']);

if (empty($slug)){
	PageBuilder::ErrorDocument(400);
	die;
}
$bdd = new DBService();
/** @var ArtworkDTO **/
$tag = $bdd->GetTag($slug);
	
if ($tag == null){
	PageBuilder::ErrorDocument(404);
	die;
}

$name = $tag->GetName();

$page = new PageBuilder();
$page->title = "Edit : $name";
$page->StartPage();
	print("<h2>Edit tag</h2>");
	$page->TagForm($tag, "update.php?tag=$slug");
$page->EndPage();

?>