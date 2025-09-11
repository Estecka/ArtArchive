<?php
/**
 * @var PageBuilder $this
 * @var TagDTO[] $tags
 * @var CategoryDTO[] $cats
 * @var bool $showEmptyCats
 */

$cats[null] = CategoryDTO::Empty();

// Apparently I'm grafting a new field onto a class, and it just works.
foreach($cats as $key=>$value)
	$cats[$key]->tags = array();

foreach($tags as $tag)
	$cats[$tag->categoryId]->tags[] = $tag;


?>
<div class="taglist">
	<?php
	foreach($cats as $cat)
	{
		$empty = empty($cat->tags);
		if ($empty && !$showEmptyCats)
		continue;
		
		$catTitle = "<h4>".$cat->GetName()."</h4>";
		if ($cat->slug != null){
			$url = URL::Category($cat->slug);
			$catTitle = "<a href=\"$url\">$catTitle</a>";
		}
		print $catTitle;
		
		if($empty)
		print("This category is empty");
		else {
			$style = empty($cat->color) ? null : "style='--cat-color:$cat->color'";
			?>
			<ul class='tagList' <?=$style?>>
			<?php
			foreach($cat->tags as $tag) {
				?>
				<li class='tagName'>
					<?php $this->TagLink($tag) ?>
				</li>
				<?php
			}
			?>
			</ul>
		<?php
		}
	}
?>
</div>