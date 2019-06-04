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
		if (!empty($tags)){
			print("<h4>Tags : </h4>");
			$this->TagList($tags, $cats, false);
		}
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
			<?=$art->date?> <br/>
			<?=$art->description?>
		</div>
	</div>
</div>
