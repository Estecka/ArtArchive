<?php
/**
 * @var TagDTO[] $tags
 * @var CategoryDTO[] $cats
 */

$cats[null] = CategoryDTO::Empty();
$cats[null]->name = "Uncategorized";

foreach($cats as $key=>$value)
	$cats[$key]->tags = array();

foreach($tags as $tag)
	$cats[$tag->categoryId]->tags[] = $tag;

	
foreach($cats as $cat) {
	$isempty = empty($cat->tags);
	if ($isempty && $cat->id < 0)
		continue;

	$h3 = $cat->GetName();
	if ($cat->slug != null){
		$url = URL::Category($cat->slug);
		$h3 = "<a href=\"$url\">$h3</a>";
	}
	?>
	<div class="inlineCategory">
		<h3 class="categoryName"><?=$h3?></h3>
		<?php
		if($isempty)
			print("This category is empty");
		else
		foreach($cat->tags as $tag) {
			$style = empty($cat->color) ? null : "style ='color: $cat->color'";
			?>
			<a href="<?=URL::Tag($tag->slug)?>" <?=$style?>><?=$tag->slug?></a>
			<br/>
			<?php
		}
		?>
	</div>
	<?php
}

?>

