<?php
require("../../../ArtArchive.php");

$artwork = ArtworkDTO::CreateFrom($_POST);
$files = isset($_POST["files"]) ? explode("\n", $_POST["files"]) : array();
$tags =  isset($_POST["add"])  ? array_keys($_POST["add"])  : false;

$bdd = new DBService();

try {
	$response = $bdd->AddArtwork($artwork);
	if (!$response){
		PageBuilder::ErrorDocument(500, "Unknown database error");
	} else {
		$bdd->AddFiles($artwork->slug, $files);
		$bdd->AddTagsToArtwork($artwork->slug, $tags);
		header("Location:".URL::Artwork($artwork->slug), false, 303);
		exit;
	}
} catch (PDOException $e) {
	print($e->getCode());
	print("<br/>");
	print($e->getMessage());
}
?>