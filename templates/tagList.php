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
	print("<h3>".$cat->GetName()."</h3>");
	foreach($cat->tags as $tag) {
		$style = empty($cat->color) ? null : "style ='color: $cat->color'";
		?>
		<a href="<?=URL::Tag($tag->slug)?>" <?=$style?>><?=$tag->slug?></a>
		<br/>
		<?php
	}
}
?>