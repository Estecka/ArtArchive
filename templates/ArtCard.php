<?php
/** 
 * @var ArtWorkDTO $art
 * @var string $art->thumbnail
 */

$name = $art->title ?? $art->slug;
?>

<a href="<?=URL::Artwork($art->slug)?>" title="<?=htmlspecialchars($name)?>"><div class="card">
	<div class="viewport">
		<?php
		if ($art->thumbnail) {
			?>
			<img src="<?=URL::Media($art->thumbnail)?>"/>
			<?php
		}
		?>
	</div>
	<h4><?=$name?></h4>
	<?=$art->date?>
</div></a>
