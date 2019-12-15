<?php
/**
 * @var PageBuilder $page
 * @var TagDTO[] $tags
 * @var CategoryDTO[] $cats
 */

$cats[null] = CategoryDTO::Empty();
$cats[null]->name = "Uncategorized";

foreach($cats as $key=>$value)
	$cats[$key]->tags = array();

foreach($tags as $tag)
	$cats[$tag->categoryId]->tags[] = $tag;

$printCat = function(CategoryDTO $c){
	$h3 = $c->GetName();
	if ($c->slug != null){
		$url = URL::Category($c->slug);
		$h3 = "<a href=\"$url\">$h3</a>";
	}
	?>
	<h3 class="categoryName"><?=$h3?></h3>
	<?php
};
$printTag = function(CategoryDTO $c, TagDTO $t){
	$style = empty($c->color) ? null : "style =\"color: $c->color\"";
	?>
	<a href="<?=URL::Tag($t->slug)?>" <?=$style?>><?=$t->slug?></a>
	<br/>
	<?php
};
	
foreach($cats as $cat) {
	$isempty = empty($cat->tags);
	if ($isempty && $cat->id < 0)
		continue;
	else {
		$page->TagLiquid($cat, $cat->tags, 6, $printCat, $printTag);
	}
}