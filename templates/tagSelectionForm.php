<?php
/**
 * @var TagDTO[] $tags Each tag is provided with an additional property `enabled`.
 * @var CategoryDTO[] $cats
 */

$cats[null] = CategoryDTO::Empty();
$cats[null]->name = "Others";

foreach($cats as $key=>$cat){
	$cats[$key]->tags = array();
}
foreach($tags as $tag){
	$cats[$tag->categoryId]->tags[] = $tag;
}

foreach($cats as $cat){
	$name = $cat->GetName();
	$style = $cat->color ? "style=\"color: $cat->color\"" : null;
	$createId = empty($cat->slug) ? "createNULL" : "create[$cat->slug]"
	?>
	<h5 <?=$style?>>♦ <?=$name?></h5>
	<textarea id="<?=$createId?>" name="<?=$createId?>" placeholder="Add new tags to this category. &#10;One slug per line."></textarea>
	<br/>
	<?php
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
}
?>