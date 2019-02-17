<?php
$name = $art["title"] ?? $art["slug"]
?>
<div>
	<a href="/art/<?=$art['slug']?>/"><h3><?=$name?></h3></a>
	<?=$art["date"]?>
</div>