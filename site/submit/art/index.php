<?php
require("../../../ArtArchive.php");

$bdd = new DBService();

$tags = $bdd->GetArtformTags(-1);

$page = new PageBuilder();
$page->title = "Submit";
$page->StartPage();

	$art = ArtworkDTO::CreateFrom($_POST);
	$page->ArtForm($art, $tags, array(), "insert.php");
	
$page->EndPage();
?>