<?php
require("../../ArtArchive.php");

$slug = value($_GET['art']);
if (empty($slug)) {
	PageBuilder::ErrorDocument(400);
	die;
}

$artwork = ArtworkDTO::CreateFrom($_POST);
$bdd = new DBService();

try {
	$response = $bdd->UpdateArtwork($slug, $artwork);
	if ($response)
		header("Location:".URL::Artwork($artwork->slug));
	else {
		PageBuilder::ErrorDocument(404);
		die;
	}
} catch (PDOException $e){
	print($e->getCode());
	print("<br/>");
	print($e->getMessage());
}
?>