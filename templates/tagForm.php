<?php
/**
 * @var TagDTO $tag
 * @var CategoryDTO[] $cats
 * @var string $action
 */

$cats = array(null => new CategoryDTO()) + $cats;
$cats[null]->name = "None";
$cats[null]->id = null;

?>

<div>
	<form action="<?=value($action)?>" method="post">
		<label for="slug">Slug</label> 
		<input id="slug" name="slug" type="text" value="<?=htmlspecialchars($tag->slug)?>"/>
		
		<br/>
		
		<label for="name">Name</label> 
		<input id="name" name="name" type="text" value="<?=htmlspecialchars($tag->name)?>"/>

		<br/>
		
		<label for="description">Descriptions</label> <br/>
		<textarea id="description" name="description"><?=htmlspecialchars($tag->description)?></textarea>

		<br/>

		Category :
		<?php
		foreach($cats as $cat){
			$checked = ($cat->id == $tag->categoryId) ? "checked" : null;
			$id = "category[$cat->slug]";
			$color = empty($cat->color) ? null : "style=\"color: $cat->color\"";
			?>
			<br/>
			<input id ="<?=$id?>" name="categoryId" type="radio" value="<?=$cat->id?>" <?=$checked?>>
			<label for="<?=$id?>" <?=$color?>><?=$cat->GetName()?></label>
			<?php
		}
		?>

		<br/>
		
		<input type="submit" value="Submit"/>
	</form>
</div>