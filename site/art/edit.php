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


$name = $art->GetName();

$page = new PageBuilder();
$page->title = "Edit : $name";
$page->StartPage();
		print("<h2>Submit artwork</h2>");
		$tags = either($_POST['tags'], array());
		$page->ArtForm($art, $tags, "update.php?art=$slug");
$page->EndPage();

?>