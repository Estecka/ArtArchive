<?php
require("../../ArtArchive.php");
ArtArchive::RequireWebmaster();

if (!empty($_POST)){
	include __ROOT__."/database/Actions/update-artwork.php";
	exit;
}

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
$tags  = $bdd->GetAllTagsByArtwork($art->id);
$cats  = $bdd->GetAllCategories();


$name = $art->GetName();

$page = new PageBuilder("Edit : $name");
$page->StartPage();
		print("<h2>Submit artwork</h2>");
		$page->ArtForm($art, $tags, $cats, $files);
$page->EndPage();

?>