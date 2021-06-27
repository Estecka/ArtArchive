<?php
/** 
 * @var PageBuilder $page
 * @var ArtworkDTO $art
 * @var TagDTO[] $tags
 * @var CategoryDTO[] $cats
 * @var string[] $files
 */

 $filesText = $files ? implode("\n", $files) : null;
$filesHint = 
	"Insert one media per line. It should be the url relative to the /storage/ folder. \n"
	."E.g: \n\n"
	."artwork1.jpg \n"
	."subfolder/artwork2.mp3 \n"
	// ."http://mydomain/storage/dankerstmeme.pdf"
	;

$extlinkHint = "One link per line. Markdown link syntax is supported.\n"
	."E.g:\n\n"
	."http://website.com/artwork \n"
	."[Link label](http://website.com/artwork)\n"
	;
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
		<textarea 
			id="description" 
			name="description" 
			placeholder="Supports any html formatting"
			rows=10
		><?=htmlspecialchars($art->description)?></textarea>

		<br/>

		<h4><label for="files">Files :</label></h4>
		<textarea 
			id="files" 
			name="files" 
			placeholder="<?=$filesHint?>"
			rows=5
		><?=htmlspecialchars($filesText)?></textarea>

		<br/>

		<h4><label for="links">External links :</label></h4>
		<textarea
			id="links"
			name="links"
			placeholder="<?=$extlinkHint?>"
			rows=5
		><?=htmlspecialchars($art->links)?></textarea>
		<br/>

		<h4>Tags :</h4>
		<?php
		$page->TagSelectionForm($tags, $cats, true);
		?>

		<br/>

		<input type="submit" value="Submit"/>
	</form>
</div>