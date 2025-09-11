<?php
/**
 * @var PageBuilder $page
 * @var TagDTO[] $tags
 * @var CategoryDTO[] $cats
 */

/**
 * TODO: Figure out if how much of this can be merged with tagList.php
 */

$cats[null] = CategoryDTO::Empty();
$cats[null]->name = "Uncategorized";

// Apparently I'm grafting a new field onto a class, and it just works.
foreach($cats as $key=>$value)
	$cats[$key]->tags = array();

foreach($tags as $tag)
	$cats[$tag->categoryId]->tags[] = $tag;

$printCat = function(CategoryDTO $c){
	$h3 = $c->GetName();
	$url = ($c->slug != null) ? URL::Category($c->slug) : null;

	if ($url!=null){
		?><a href="<?=$url?>"><?php
	}

	?><h3 class="categoryName"><?=$h3?></h3><?php

	if ($url!=null){
		?></a><?php
	}
};
$printTag = function(CategoryDTO $c, TagDTO $t){
	global $page;
	$page->TagLink($t);
};

print "<div class='masonry'>";
foreach($cats as $cat) {
	$isempty = empty($cat->tags);
	if ($isempty && $cat->id < 0)
		continue;
	else {
		$page->TagLiquid($cat, $cat->tags, $printCat, $printTag);
	}
}
print "</div>";
