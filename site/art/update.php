<?php
require("../../ArtArchive.php");

$slug = value($_GET['art']);
if (empty($slug)) {
	http_response_code(400);
	print (400);
	die;
}

$artwork = ArtworkDTO::CreateFrom($_POST);
$bdd = new DBService();

try {
	$response = $bdd->UpdateArtwork($slug, $artwork);
	if ($response)
		header("Location:".URL::Artwork($artwork->slug));
	else {
		http_response_code(404);
		print (404);
	}
} catch (PDOException $e){
	print($e->getCode());
	print("<br/>");
	print($e->getMessage());
}
?>