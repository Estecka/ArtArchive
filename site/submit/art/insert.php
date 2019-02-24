<?php
require("../../../ArtArchive.php");

$artwork = ArtworkDTO::CreateFrom($_POST);
$bdd = new DBService();

try {
	$response = $bdd->AddArtwork($artwork);
	header("Location:".URL::Home(), false, 303);
	exit;
} catch (PDOException $e) {
	print($e->getCode());
	print("<br/>");
	print($e->getMessage());
}
?>