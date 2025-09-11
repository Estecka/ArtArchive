<?php
/**
 * @var CategoryDTO $cat
 * @var TagDTO[] $tags
 * @var function $printCat function(CategoryDTO) 
 * @var function $printTag function(TagDTO)
 */

/**
 * TODO: Figure out if how much of this cna be merged into tagList.php
 */

$rowMax = ArtArchive::$settings['tagMasonry'] ? 0 : ArtArchive::$settings['tagLiquidity'];
$empty = empty($tags);

for($col=0; $col==0 || current($tags); $col++){
	$hidden = $col ? "style=\"visibility: hidden\"" : null;
	$style=$cat->color ? "style='--cat-color:$cat->color'" : null;
	?>
	<div class="inlineCategory" <?=$style?>>
		<div <?=$hidden?>>
			<?=$printCat($cat)?>
		</div>
		<ul class='tagList'>
		<?php
		if (!$col && !current($tags)) {
			?><i class='emptyNotice'>This category is empty.</i><?php
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
