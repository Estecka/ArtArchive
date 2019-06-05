<?php
/** @var ArtWorkDTO */
$name = $art->title ?? $art->slug;
?>
<a href="<?=URL::Artwork($art->slug)?>"><div class="card">
	<div class="viewport">
		<img/>
	</div>
	<h4 class="row"><?=$name?></h4>
	<?=$art->date?>
</div></a>
