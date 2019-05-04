<?php
require("../../../ArtArchive.php");

$artwork = ArtworkDTO::CreateFrom($_POST);
$files = isset($_POST["files"]) ? explode("\n", $_POST["files"]) : array();
$tags =  isset($_POST["add"])  ? array_keys($_POST["add"])  : false;
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

$bdd = new DBService();

try {
	$response = $bdd->AddArtwork($artwork);
	if (!$response){
		PageBuilder::ErrorDocument(500, "Unknown database error");
	} else {
		$bdd->AddFiles($artwork->slug, $files);
		foreach($create as $cat=>$tags){
			$bdd->InsertTagsBulk($cat, $tags);
		}
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