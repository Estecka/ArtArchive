<?php 
/** 
 * @var PageBuilder $this
 * @var ArtworkDTO $art
 * @var TagDTO[] $tags 
 * @var CategoryDTO[] $cats
*/
?>

<a href="<?=URL::EditArt($art->slug)?>">Edit</a> | <a href="<?=URL::DeleteArt($art->slug)?>">Delete</a>
<hr>
<h2><?=$art->GetName()?></h2>
<?=$art->date?> <br/>
<?=$art->description?>

<?php
if (!empty($tags)){
	print("<h4>Tags : </h4>");
	$this->TagList($tags, $cats);
}
?>