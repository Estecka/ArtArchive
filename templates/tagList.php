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


?>
<div class="taglist">
	<?php
	foreach($cats as $cat)
	{
		$empty = empty($cat->tags);
		if ($empty && !$showEmptyCats)
		continue;
		
		$h3 = $cat->GetName();
		if ($cat->slug != null){
			$url = URL::Category($cat->slug);
			$h3 = "<a href=\"$url\">$h3</a>";
		}
		print("<h4>$h3</h4>");
		
		if($empty)
		print("This category is empty");
		else {
			?>
			<ul>
			<?php
			foreach($cat->tags as $tag) {
				$style = empty($cat->color) ? null : "style ='color: $cat->color'";
				?>
				<li>
					<a 
						href="<?=URL::Tag($tag->slug)?>" 
						title="<?=$tag->slug?>"
						<?=$style?>
					>
						<?=$tag->slug?>
					</a>
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