<?php
/**
 * @var TagDTO[] $tags
 * @var CategoryDTO[] $cats
 * @var bool $showEmptyCats
 */

$cats[null] = CategoryDTO::Empty();

foreach($cats as $key=>$value)
	$cats[$key]->tags = array();

foreach($tags as $tag)
	$cats[$tag->categoryId]->tags[] = $tag;



foreach($cats as $cat) {
	$empty = empty($cat->tags);
	if ($empty && !$showEmptyCats)
		continue;

	$h3 = $cat->GetName();
	if ($cat->slug != null){
		$url = URL::Category($cat->slug);
		$h3 = "<a href=\"$url\">$h3</a>";
	}
	print("<h3>$h3</h3>");

	if($empty)
		print("This category is empty");
	else
	foreach($cat->tags as $tag) {
		$style = empty($cat->color) ? null : "style ='color: $cat->color'";
		?>
		<a href="<?=URL::Tag($tag->slug)?>" <?=$style?>><?=$tag->slug?></a>
		<br/>
		<?php
	}
}
?>