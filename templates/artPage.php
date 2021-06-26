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

?>
<div class="row">
	<div class="column margin">
		<?php
		if (!empty($tags))
			$this->TagList($tags, $cats, false);
		if (!empty($tags) && !empty($art->links))
			print "<hr/>";
		if (!empty($art->links))
			$this->LinkList($art->links);
		?>
	</div>
	<div class="column artwork">
		<div class="media">
			<h2><?=$art->GetName()?></h2>
			<?php
			if (empty($files))
				print "There are no files attached to this artwork.";
			else foreach($files as $path){
				$this->Media($path);
				print "<br/>";
			}
			?>
		</div>
		<hr>
		<div class="description">
			<h2><?=$art->GetName()?></h2>
			<hr/>
			<?=$art->date?> <br/>
			<p>
				<?=str_replace("\n", "<br/>", $art->description)?>
			<p>
		</div>
	</div>
</div>
