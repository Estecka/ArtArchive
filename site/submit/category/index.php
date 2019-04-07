<?php
require("../../../ArtArchive.php");

$page = new PageBuilder();
$page->title = "Submit";
$page->StartPage();

	$cat = CategoryDTO::CreateFrom($_POST);
	$page->CategoryForm($cat, "insert.php");
	
$page->EndPage();
?>