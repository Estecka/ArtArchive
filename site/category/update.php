<?php
require("../../ArtArchive.php");

$slug = value($_GET['category']);
if (empty($slug)) {
	PageBuilder::ErrorDocument(400);
	die;
}

$cat = CategoryDTO::CreateFrom($_POST);
$bdd = &ArtArchive::$database;

try {
	$response = $bdd->UpdateCategory($slug, $cat);
	if ($response)
		header("Location:".URL::Category($cat->slug));
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