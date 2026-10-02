<?php
/** 
 * @param PageBuilder $page
 * @param ArtworkDTO $art
 * @param TagDTO[] $tags List of all available tags. Each tag should provide an additional `enabled`  property.
 * @param CategoryDTO[] $cats List of all available categories.
 * @param string[] $files The urls this artwork's files.
 */
function template_artworkForm(
	PageBuilder $page,
	ArtworkDTO $art,
	array $tags,
	array $cats,
	array $files,
	$action = null
)
{
	$filesText = $files ? implode("\n", $files) : null;
	$filesHint = <<<EOF
		Insert one media per line. It should be the url relative to the /storage/ folder.
		E.g:\n
		artwork1.jpg
		subfolder/artwork2.mp3
		EOF;

	$extlinkHint = <<<EOF
		One link per line. Markdown link syntax is supported.
		E.g:\n
		http://website.com/artwork
		[Link label](http://website.com/artwork)
		EOF;
	?>


	<div>
		<form action="<?=value($action)?>" method="post">

			<table>
				<tr>
					<td><label for="slug">Slug</label></td>
					<td><input id="slug" name="slug" type="text" value="<?=htmlspecialchars($art->slug)?>"/></td>
				</tr>
				<tr>
					<td><label for="title">Title</label></td>
					<td><input id="title" name="title" type="text" value="<?=htmlspecialchars($art->title)?>"/></td>
				</tr>
				<tr>
					<td><label for="date">Date</label></td>
					<td><input id="date" name="date" type="date"  value="<?=$art->date?>"/></td>
				</tr>
			</table>

			<table>
				<tr>
					<td><label for="thumbUrl">Thumbnail</label></td>
					<td><input id="thumbUrl" name="thumbUrl" type="text" value="<?=$art->thumbUrl?>" placeholder="Auto"/></td>
				</tr>
				<tr>
					<td><label for="thumbFocusX">Thumbnail X</label></td>
					<td><input id="thumbFocusX" name="thumbFocusX" type="number" min=0 max=100 value="<?=$art->thumbFocusX?>" placeholder="50%"/> %</td>
				</tr>
				<tr>
					<td><label for="thumbFocusY">Thumbnail Y</label></td>
					<td><input id="thumbFocusY" name="thumbFocusY" type="number" min=0 max=100 value="<?=$art->thumbFocusY?>" placeholder="30%"/> %</td>
				</tr>
			</table>

			<div style="display:grid; grid-template-columns:auto auto;" >
				<div>
					<h4><label for="files"><img src="/resources/media.png"/> Media :</label></h4>
					<textarea 
						id="files" 
						name="files" 
						placeholder="<?=$filesHint?>"
						rows=5
					><?=htmlspecialchars($filesText)?></textarea>
				</div>
				<div>
					<h4><label for="links"><img src="/resources/link.png"/> External links :</label></h4>
					<textarea
						id="links"
						name="links"
						placeholder="<?=$extlinkHint?>"
						rows=5
					><?=htmlspecialchars($art->links)?></textarea>
				</div>
			</div>

			<h4><label for="description">Descriptions :</label></h4>
			<textarea 
				id="description" 
				name="description" 
				placeholder="Supports any html formatting and basic markdown syntax."
				rows=10
			><?=htmlspecialchars($art->description)?></textarea>

			<div>
				<h4><?php $page->MaskedImage("/resources/icon/tag-black.png")?> Tags :</h4>
				<?php
				$page->TagSelectionForm($tags, $cats, true);
				?>
			</div>
			<input type="submit" value="Submit"/>
		</form>
	</div>
	<?php
}
