<?php
require("../ArtArchive.php");
$bdd = &ArtArchive::$database;

/** 
 * @var TagDTO[] $tags
 * @var CategoryDTO[] $cats
 * @var TagDTO[][] $tagsByCat
*/
$tags = $bdd->GetAllTags();
$cats = $bdd->GetAllCategories();

$page = new PageBuilder("Tags and Categories");
$page->StartPage();
	$page->TagTable($tags, $cats);
$page->EndPage();
?>
