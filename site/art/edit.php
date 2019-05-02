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
$files = $bdd->GetFiles($art->id);
$tags  = $bdd->GetAllTagsByArtwork($art->id);
$cats  = $bdd->GetAllCategories();


$name = $art->GetName();

$page = new PageBuilder();
$page->title = "Edit : $name";
$page->StartPage();
		print("<h2>Submit artwork</h2>");
		$page->ArtForm($art, $tags, $cats, $files, "update.php?art=$slug");
$page->EndPage();

?>