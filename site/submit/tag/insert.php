<?php
require("../../../ArtArchive.php");

$tag = TagDTO::CreateFrom($_POST);
$bdd = new DBService();

try {
	$response = $bdd->InsertTag($tag);
	header("Location:".URL::Home(), false, 303);
	exit;
} catch (PDOException $e) {
	print($e->getCode());
	print("<br/>");
	print($e->getMessage());
}
?>