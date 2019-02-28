<?php 
/** @var $art ArtworkDTO */
/** @var $tags TagDTO[] */
?>

<a href="<?=URL::EditArt($art->slug)?>">Edit</a> | <a href="<?=URL::DeleteArt($art->slug)?>">Delete</a>
<hr>
<h2><?=$art->GetName()?></h2>
<?=$art->date?> <br/>
<?=$art->description?>

<?php if ($tags != null) { ?>
	<div>
		<h4>Tags : </h4>
		<?php foreach($tags as $tag){ ?>
			<a href="<?=URL::Tag($tag->slug)?>"><?=$tag->slug?></a> <br/>
		<?php } ?>
	</div>
<?php } ?>