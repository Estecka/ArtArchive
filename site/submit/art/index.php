<?php
require("../../../ArtArchive.php");

$page = new PageBuilder();
$page->title = "Submit";
$page->StartPage();

	$art = ArtworkDTO::CreateFrom($_POST);
	$page->ArtForm($art, "insert.php");
	
$page->EndPage();
?>