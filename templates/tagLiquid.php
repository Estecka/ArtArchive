<?php
/**
 * @var CategoryDTO $cat
 * @var TagDTO[] $tags
 * @var function $printCat function(CategoryDTO) 
 * @var function $printTag function(TagDTO)
 */

$rowMax = ArtArchive::$settings['tagMasonry'] ? 0 : ArtArchive::$settings['tagLiquidity'];
$empty = empty($tags);

for($col=0; $col==0 || current($tags); $col++){
	$hidden = $col ? "style=\"visibility: hidden\"" : null;
	?>
	<div class="inlineCategory" style="--cat-color: <?=either($cat->color, "black")?>">
		<div <?=$hidden?>>
			<?=$printCat($cat)?>
		</div>
		<ul>
		<?php
		if (!$col && !current($tags)) {
			print("This category is empty.");
		}
		for($row=0; (!$rowMax || $row<$rowMax) && $tag = current($tags); $row++, next($tags)){
			?>
			<li class="tagName"><?=$printTag($cat, $tag)?></li>
			<?php
		}
		?>
		</ul>
	</div>
	<?php
}
