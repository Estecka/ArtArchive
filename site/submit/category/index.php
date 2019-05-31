<?php
require("../../../ArtArchive.php");
ArtArchive::RequireWebmaster();

if (!empty($_POST)){
	include __ROOT__."/database/Actions/insert-category.php";
	exit;
}

$page = new PageBuilder();
$page->title = "Submit";
$page->StartPage();

	$cat = CategoryDTO::CreateFrom($_POST);
	$page->CategoryForm($cat);
	
$page->EndPage();
?>