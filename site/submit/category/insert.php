<?php
require("../../../ArtArchive.php");

$cat = CategoryDTO::CreateFrom($_POST);
$bdd = new DBService();

try {
	$response = $bdd->InsertCategory($cat);
	header("Location:".URL::TagsHome(), false, 303);
	exit;
} catch (PDOException $e) {
	print($e->getCode());
	print("<br/>");
	print($e->getMessage());
}
?>