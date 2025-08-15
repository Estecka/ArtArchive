<?php
/** 
 * @var ArtWorkDTO $art
 * @var string $art->thumbnail
 */

$name = $art->title ?? $art->slug;
?>

<a href="<?=URL::Artwork($art->slug)?>"><div class="card">
	<div class="viewport">
		<?php
		if ($art->thumbnail) {
			?>
			<img src="<?=URL::Media($art->thumbnail)?>"/>
			<?php
		}
		?>
	</div>
	<h4 class="row"><?=$name?></h4>
	<?=$art->date?>
</div></a>
