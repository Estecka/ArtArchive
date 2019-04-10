<?php
require("../../ArtArchive.php");

$slug = value($_GET['art']);

if (empty($slug)){
	PageBuilder::ErrorDocument(400);
	die;
}
$bdd = new DBService();
/** @var ArtworkDTO **/
$art = $bdd->GetArtwork($slug);

if ($art == null){
	PageBuilder::ErrorDocument(404);
	die;
}

$tags = $bdd->GetTagsFromArtwork($art->id);
$cats = empty($tags) ? null : $bdd->GetAllCategories();

$name = $art->title ?? $slug;

$page = new PageBuilder();
$page->title = $name;
$page->StartPage();
	$page->ArtPage($art, $tags, $cats);
$page->EndPage();
?>