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
$create = either($_POST["create"], array());
if (isset($_POST["createNULL"]))
	$create[null] = $_POST["createNULL"];

foreach($create as $cat=>$tags){
	if (!empty($tags)){
		$tags = explode("\n", $tags);
		foreach($tags as $key=>$tag){
			$tags[$key] = $tag = trim($tag);
			if (!empty($tag))
				$add[] = $tag;
		}
	}

	if ($tags)
		$create[$cat] = $tags;
	else
		unset($create[$cat]);
}

$bdd = &ArtArchive::$database;

try {
	$bdd->StartTransaction();
	$response = $bdd->UpdateArtwork($slug, $artwork);
	if (!$response) {
		$bdd->Rollback();
		PageBuilder::ErrorDocument(404, "Artwork not found");
		die;
	}
	else {
		$slug = $artwork->slug;
		$bdd->SetFiles($slug, $files);
		$bdd->RemoveTagsFromArtwork($slug, $keep, true);
		foreach($create as $cat=>$tags){
			$bdd->InsertTagsBulk($cat, $tags);
		}
		if ($add)
			$bdd->AddTagsToArtwork($slug, $add, true);
		$bdd->CommitTransaction();
		header("Location:".URL::Artwork($artwork->slug));
	}
} catch (PDOException $e){
	print($e->getCode());
	print("<br/>");
	print($e->getMessage());
	$bdd->Rollback();
}
?>