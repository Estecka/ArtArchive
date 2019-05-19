<?php
require("../../ArtArchive.php");

$slug = value($_GET['tag']);
if (empty($slug)) {
	PageBuilder::ErrorDocument(400);
	die;
}

$tag = TagDTO::CreateFrom($_POST);
$bdd = &ArtArchive::$database;

try {
	$response = $bdd->UpdateTag($slug, $tag);
	if ($response)
		header("Location:".URL::Tag($tag->slug));
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