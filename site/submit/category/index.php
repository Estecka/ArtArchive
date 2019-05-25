<?php
require("../../../ArtArchive.php");
ArtArchive::RequireWebmaster();

$page = new PageBuilder();
$page->title = "Submit";
$page->StartPage();

	$cat = CategoryDTO::CreateFrom($_POST);
	$page->CategoryForm($cat, "insert.php");
	
$page->EndPage();
?>