<?php
require("../../../ArtArchive.php");
ArtArchive::RequireWebmaster();

$tag = TagDTO::CreateFrom($_POST);
$bdd = &ArtArchive::$database;

try {
	$response = $bdd->InsertTag($tag);
	header("Location:".URL::TagsHome(), false, 303);
	exit;
} catch (PDOException $e) {
	print($e->getCode());
	print("<br/>");
	print($e->getMessage());
}
?>