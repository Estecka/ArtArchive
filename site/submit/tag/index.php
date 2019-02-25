<?php
require("../../../ArtArchive.php");

$page = new PageBuilder();
$page->title = "Submit";
$page->StartPage();

	$tag = TagDTO::CreateFrom($_POST);
	$page->TagForm($tag, "insert.php");
	
$page->EndPage();
?>