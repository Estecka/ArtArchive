<?php
require("../../ArtArchive.php");

$slug = value($_GET['art']);

if (empty($slug)){
	PageBuilder::ErrorDocument(400);
	die;
}
$bdd = &ArtArchive::$database;
/** @var ArtworkDTO **/
$art = $bdd->GetArtwork($slug);

if ($art == null){
	PageBuilder::ErrorDocument(404);
	die;
}

$files = $bdd->GetFiles($art->id);
$tags = $bdd->GetTagsFromArtwork($art->id);
$cats = empty($tags) ? null : $bdd->GetAllCategories();

$name = $art->title ?? $slug;

$page = new PageBuilder($name);
$page->openGraph->description = $art->date;
foreach($files as $file)
	$page->openGraph->AddMedia($file);
$page->StartPage();
	$page->ArtPage($art, $tags, $cats, $files);
$page->EndPage();
?>