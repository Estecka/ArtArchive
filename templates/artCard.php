<?php
/** 
 * @var ArtWorkDTO $art
 */

$name = $art->title ?? $art->slug;

?>
<a href="<?=URL::Artwork($art->slug)?>" title="<?=htmlspecialchars($name)?>"><div class="card">
	<div class=viewport>
		<?php
		$thumbX = either($art->thumbFocusX, 50);
		$thumbY = either($art->thumbFocusY, 30);

		if ($art->thumbUrl) {
			?>
			<img
				src="<?=URL::Thumb($art->thumbUrl)?>"
				style="object-position: <?=$thumbX?>% <?=$thumbY?>%"
			/>
			<?php
		}
		?>
	</div>
	<div class=details>
		<h4 class=artTitle><?=$name?></h4>
		<span class=date><?=$art->date?></span>
	</div>
</div></a>
