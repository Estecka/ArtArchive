<?php
/**
 * @var PageBuilder $page
 * @var TagDTO[] $tags Each tag is provided with an additional property `enabled`.
 * @var CategoryDTO[] $cats
 * @var bool $showEmptyCats
 */

$printCat = function(CategoryDTO $c) use ($showEmptyCats){
	$name = $c->GetName();
	$style = $c->color ? "style=\"color: $c->color\"" : null;
	$createId = empty($c->slug) ? "createNULL" : "create[$c->slug]";
	print("<h4 $style>$name</h4>");
	if ($showEmptyCats) {
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
	$inputName = $t->enabled ? "keep" : "add";
	$inputName.= "[$t->slug]";
	?>
	<label class="tagName" for="<?=$inputName?>">
		<input type="checkbox" id="<?=$inputName?>" name ="<?=$inputName?>" <?=$t->enabled?"checked":null?> />
		<?=$t->slug?>
	</label>
	<?php
};

$page->LiquidTable(
	$tags, $cats,
	$printTag, $printCat,
	"Others",
	$showEmptyCats
);
?>