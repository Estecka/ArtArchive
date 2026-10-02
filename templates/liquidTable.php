<?php
/**
 * Handles the printing of ALL categories in a tag list.
 * See: liquidCategory.php
 * 
 * @var PageBuilder $page
 * @var TagDTO[] $tags
 * @var CategoryDTO[] $cats
 * @var callable $printTag
 * @var callable $printCat
 * @var string $nullCatName
 * @var bool $showEmptyCats
 */

$cats[null] = CategoryDTO::Empty();
$cats[null]->name = $nullCatName;

// Apparently I'm grafting a new field onto a class, and it just works.
foreach($cats as $key=>$cat){
	$cats[$key]->tags = array();
}
foreach($tags as $tag){
	$cats[$tag->categoryId]->tags[] = $tag;
}

?>
<div class="masonry tagForm">
	<?php
	foreach($cats as $cat){
		if (empty($cat->tags) && !$showEmptyCats)
			continue;
		else
			$page->LiquidCategory($cat, $cat->tags, $printTag, $printCat);
	}
	?>
</div>
