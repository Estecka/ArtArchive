<?php
/** 
 * @var ArtWorkDTO $art
 * @var string $art->thumbnail
 */

$name = $art->title ?? $art->slug;

?>
<a href="<?=URL::Artwork($art->slug)?>" title="<?=htmlspecialchars($name)?>"><div class="card">
	<div class=viewport>
		<?php
		if ($art->thumbnail) {
			?>
			<img src="<?=URL::Thumb($art->thumbnail)?>"/>
			<?php
		}
		?>
	</div>
	<div class=details>
		<h4 class=artTitle><?=$name?></h4>
		<span class=date><?=$art->date?></span>
	</div>
</div></a>
