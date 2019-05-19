<?php
require("../../ArtArchive.php");

$slug = value($_GET['category']);

if (empty($slug)){
	PageBuilder::ErrorDocument(400);
	die;
}
$bdd = &ArtArchive::$database;
/** @var ArtworkDTO **/
$cat = $bdd->GetCategoryBySlug($slug);

if ($cat == null){
	PageBuilder::ErrorDocument(404);
	die;
}

$name = $cat->GetName();

$page = new PageBuilder();
$page->title = "Edit Category : $name";
$page->StartPage();
	print("<h2>Edit Category</h2>");
	$page->CategoryForm($cat, "update.php?category=$slug");
$page->EndPage();

?>