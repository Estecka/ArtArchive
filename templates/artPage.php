<?php 
/**@var $art ArtworkDTO */
?>

<a href="<?=URL::EditArt($art->slug)?>">Edit</a> | <a href="<?=URL::DeleteArt($art->slug)?>">Delete</a>
<hr>
<h1><?=$art->GetName()?></h1>
<?=$art->date?> <br/>
<?=$art->description?>