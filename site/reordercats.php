<?php
require("../Artarchive.php");
$bdd = &ArtArchive::$database;

if (sizeof($_POST) <= 0) {
	PageBuilder::ErrorDocument(400, "No data");
	die;
}

try {
	$bdd->ReorderCategories($_POST);
	header("Location:".URL::OrderCategory(), false, 303);
	exit;
} catch (PDOException $e){
	print($e->getCode());
	print("<br/>");
	print($e->getMessage());
}

?>