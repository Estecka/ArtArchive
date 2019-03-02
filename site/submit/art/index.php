<?php
require("../../../ArtArchive.php");

$page = new PageBuilder();
$page->title = "Submit";
$page->StartPage();

	$art = ArtworkDTO::CreateFrom($_POST);
	$tags = either($_POST['tags'], array());
	$page->ArtForm($art, $tags, "insert.php");
	
$page->EndPage();
?>