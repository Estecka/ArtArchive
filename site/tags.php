<?php
require("../Artarchive.php");
$bdd = &ArtArchive::$database;

/** 
 * @var TagDTO[] $tags
 * @var CategoryDTO[] $cats
 * @var TagDTO[][] $tagsByCat
*/
$tags = $bdd->GetAllTags();
$cats = $bdd->GetAllCategories();

$page = new PageBuilder();
$page->title = "Tags and Categories";
$page->StartPage();
?>
<a href="<?=URL::SubmitTag()?>">Create Tag</a>
 | 
<a href="<?=URL::SubmitCategory()?>">Create Category</a>
 | 
<a href="<?=URL::OrderCategory()?>">Reorder Category</a>
<hr/>
<?php

$page->TagList($tags, $cats, true);

$page->EndPage();
?>
