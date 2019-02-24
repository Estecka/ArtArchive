<?php
$name = $art["title"] ?? $art["slug"]
?>
<div>
	<a href="<?=URL::Artwork($art['slug'])?>"><h3><?=$name?></h3></a>
	<?=$art["date"]?>
</div>