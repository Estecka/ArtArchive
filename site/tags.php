<?php
require("../Artarchive.php");
$bdd = new DBService();

/** 
 * @var TagDTO[] $tags
 * @var CategoryDTO[] $cats
 * @var TagDTO[][] $tagsByCat
*/
$tags = $bdd->GetAllTags();
$cats = $bdd->GetAllCategories();

$page = new PageBuilder();
$page->StartPage();
?>
<a href="<?=URL::SubmitTag()?>">Create</a>
<hr/>
<?php

$page->TagList($tags, $cats);

$page->EndPage();
?>
