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

$page = new PageBuilder();
$page->title = $name;
$page->previewDescription = $art->date;
foreach($files as $file)
	$page->AddPreviewImage($file, $name);
$page->StartPage();
	$page->ArtPage($art, $tags, $cats, $files);
$page->EndPage();
?>