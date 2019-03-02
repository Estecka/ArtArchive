<?php
/** @var $art ArtworkDTO */
/** @var $tags TagDTO[]*/
?>

<div>
	<form action="<?=value($action)?>" method="post">
		<label for="slug">Slug</label> 
		<input id="slug" name="slug" type="text" value="<?=$art->slug?>"/>
		
		<br/>
		
		<label for="title">Title</label> 
		<input id="title" name="title" type="text" value="<?=$art->title?>"/>

		<br/>

		<label for="date">Date</label> 
		<input id="date" name="date" type="date"  value="<?=$art->date?>"/>

		<br/>
		
		<label for="description">Descriptions</label> <br/>
		<textarea id="description" name="description"><?=$art->description?></textarea>

		<br/>
		
		<input type="submit" value="Submit"/>
	</form>
</div>