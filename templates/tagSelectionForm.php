<?php
/**
 * @var PageBuilder $page
 * @var TagDTO[] $tags Each tag is provided with an additional property `enabled`.
 * @var CategoryDTO[] $cats
 * @var bool $allowInserts
 */

$cats[null] = CategoryDTO::Empty();
$cats[null]->name = "Others";

foreach($cats as $key=>$cat){
	$cats[$key]->tags = array();
}
foreach($tags as $tag){
	$cats[$tag->categoryId]->tags[] = $tag;
}

$printCat = function(CategoryDTO $c) use ($allowInserts){
	$name = $c->GetName();
	$style = $c->color ? "style=\"color: $c->color\"" : null;
	$createId = empty($c->slug) ? "createNULL" : "create[$c->slug]";
	print("<h4 $style>$name</h4>");
	if ($allowInserts) {
		?>
		<textarea 
			id="<?=$createId?>" 
			name="<?=$createId?>" 
			placeholder="Create new tags here, &#10;one slug per line."
			rows=1
		></textarea>
		<br/>
		<?php
	}
};
$printTag = function(CategoryDTO $c, TagDTO $t){
	$style = $c->color ? "style=\"color: $c->color\"" : null;
	$inputName = $t->enabled ? "keep" : "add";
	$inputName.= "[$t->slug]";
	?>
	<input type="checkbox" id="<?=$inputName?>" name ="<?=$inputName?>" <?=$t->enabled?"checked":null?>/>
	<label for="<?=$inputName?>" <?=$style?>><?=$t->slug?></label>
	<br/>
	<?php
};

print "<div class='masonry'>";
foreach($cats as $cat){
	if (empty($cat->tags) && !$allowInserts)
		continue;
	else
		$page->TagLiquid($cat, $cat->tags, 6, $printCat, $printTag);
}
print "</div>"
?>