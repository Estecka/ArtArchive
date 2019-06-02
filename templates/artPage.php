<?php 
/** 
 * @var PageBuilder $this
 * @var ArtworkDTO $art
 * @var TagDTO[] $tags 
 * @var CategoryDTO[] $cats
 * @var string[] $files 
*/

if (ArtArchive::$isWebmaster) 
{
	?>
	<a href="<?=URL::EditArt($art->slug)?>">Edit</a> | <a href="<?=URL::DeleteArt($art->slug)?>">Delete</a>
	<hr>
	<?php
}

if (empty($files))
	print "There are no files attached to this artwork.";
else foreach($files as $path){
	$this->Media($path);
	print "<br/>";
}
?>

<h2><?=$art->GetName()?></h2>
<?=$art->date?> <br/>
<?=$art->description?>

<?php
if (!empty($tags)){
	print("<h4>Tags : </h4>");
	$this->TagList($tags, $cats, false);
}
?>