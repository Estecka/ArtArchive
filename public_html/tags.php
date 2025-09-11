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
	?><article class='tagTable'><?php
		$page->TagTable($tags, $cats);
	?></article><?php
$page->EndPage();
?>
