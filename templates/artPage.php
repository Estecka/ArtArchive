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
	<nav>
		<a href="<?=URL::EditArt($art->slug)?>">Edit</a>
		 |
		<a href="<?=URL::DeleteArt($art->slug)?>" class=danger>Delete</a>
		<hr>
	</nav>
	<?php
}

?>
<article class='artPage row'>
	<section class='meta column margin'>
		<?php
		if (!empty($tags)){
			?><section class='tagList'><?php
				$this->TagList($tags, $cats, false);
			?></section><?php
		}

		if (!empty($tags) && !empty($art->links))
			print "<hr/>";

		if (!empty($art->links)){
			?>
			<section class="extlinkslist">
				<h4>External links</h4>
				<?php $this->LinkList($art->links); ?>
			</section>
			<?php
		}
		?>
	</section>

	<div class="column artwork">
		<section class="media">
			<h2><?=$art->GetName()?></h2>
			<?php
			if (empty($files))
				print "There are no files attached to this artwork.";
			else foreach($files as $path){
				$this->Media($path);
				print "<br/>";
			}
			?>
		</section>

		<hr>

		<section class="description">
			<h2><?=$art->GetName()?></h2>
			<hr/>
			<?=$art->date?> <br/>
			<p>
				<?=Markdown::MarkdownToHtml($art->description)?>
			<p>
		</section>
	</div>
</article>
