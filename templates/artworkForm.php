<?php
/** 
 * @var PageBuilder $page
 * @var ArtworkDTO $art
 * @var TagDTO[] $tags
 * @var CategoryDTO[] $cats
 * @var string[] $files
 */

 $filesText = $files ? implode("\n", $files) : null;
?>

<div>
	<form action="<?=value($action)?>" method="post">
		<label for="slug">Slug</label> 
		<input id="slug" name="slug" type="text" value="<?=htmlspecialchars($art->slug)?>"/>
		
		<br/>
		
		<label for="title">Title</label> 
		<input id="title" name="title" type="text" value="<?=htmlspecialchars($art->title)?>"/>

		<br/>

		<label for="date">Date</label> 
		<input id="date" name="date" type="date"  value="<?=$art->date?>"/>

		<br/>
		
		<label for="description">Descriptions</label> <br/>
		<textarea id="description" name="description"><?=htmlspecialchars($art->description)?></textarea>

		<br/>

		<h4><label for="files">Files :</label></h4>
		<textarea id="files" name="files"><?=htmlspecialchars($filesText)?></textarea>

		<br/>

		<h4>Tags :<h4>
		<?php
		$page->TagSelectionForm($tags, $cats, true);
		?>

		<br/>

		<input type="submit" value="Submit"/>
	</form>
</div>