<?php
require("../../ArtArchive.php");

$slug = value($_GET['art']);
if (empty($slug)) {
	PageBuilder::ErrorDocument(400);
	die;
}

$artwork = ArtworkDTO::CreateFrom($_POST);
$keep = isset($_POST["keep"]) ? array_keys($_POST["keep"]) : array();
$add =  isset($_POST["add"])  ? array_keys($_POST["add"])  : false;
$files= isset($_POST["files"])? explode("\n", $_POST["files"]) : null;

$bdd = new DBService();

try {
	$bdd->StartTransaction();
	$response = $bdd->UpdateArtwork($slug, $artwork);
	if (!$response) {
		$bdd->Rollback();
		PageBuilder::ErrorDocument(404);
		die;
	}
	else {
		$slug = $artwork->slug;
		$bdd->SetFiles($slug, $files);
		$bdd->RemoveTagsFromArtwork($slug, $keep, true);
		if ($add)
			$bdd->AddTagsToArtwork($slug, $add, true);
		$bdd->CommitTransaction();
	}
	header("Location:".URL::Artwork($artwork->slug));
} catch (PDOException $e){
	print($e->getCode());
	print("<br/>");
	print($e->getMessage());
	$bdd->Rollback();
}
?>