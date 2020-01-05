<?php
require("../../ArtArchive.php");
ArtArchive::RequireWebmaster();

if (!empty($_POST)){
	include __ROOT__."/database/Actions/update-category.php";
	exit;
}

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

$page = new PageBuilder("Edit Category : $name");
$page->StartPage();
	print("<h2>Edit Category</h2>");
	$page->CategoryForm($cat);
$page->EndPage();

?>