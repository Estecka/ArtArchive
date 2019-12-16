<?php
/**
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

print "<div class='masonry'>";
foreach($cats as $cat){
	$empty = empty($cat->tags);
	if ($empty && !$allowInserts)
		continue;

	?><div class="inlineCategory"><?php
	$name = $cat->GetName();
	$style = $cat->color ? "style=\"color: $cat->color\"" : null;
	$createId = empty($cat->slug) ? "createNULL" : "create[$cat->slug]";

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
	if (empty($cat->tags))
		print("This category has not tags :(");
	else
	foreach($cat->tags as $tag){
		$inputName = $tag->enabled ? "keep" : "add";
		$inputName.= "[$tag->slug]";
		?>
		<input type="checkbox" id="<?=$inputName?>" name ="<?=$inputName?>" <?=$tag->enabled?"checked":null?>/>
		<label for="<?=$inputName?>" <?=$style?>><?=$tag->slug?></label>
		<br/>
		<?php
	}
	?></div><?php
}
print "</div>"
?>