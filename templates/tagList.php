<?php
/**
 * @var TagDTO[] $tags
 * @var CategoryDTO[] $cats
 */

$cats[null] = CategoryDTO::Empty();

foreach($cats as $key=>$value)
	$cats[$key]->tags = array();

foreach($tags as $tag)
	$cats[$tag->categoryId]->tags[] = $tag;



foreach($cats as $cat) if(!empty($cat->tags))  {
	$h3 = $cat->GetName();
	if ($cat->slug != null){
		$url = URL::Category($cat->slug);
		$h3 = "<a href=\"$url\">$h3</a>";
	}
	print("<h3>$h3</h3>");
	foreach($cat->tags as $tag) {
		$style = empty($cat->color) ? null : "style ='color: $cat->color'";
		?>
		<a href="<?=URL::Tag($tag->slug)?>" <?=$style?>><?=$tag->slug?></a>
		<br/>
		<?php
	}
}
?>