<?php
/** 
 * @var ArtworkDTO $art
 * @var TagDTO[] $tags
 * @var CategoryDTO[] $cats
 * @var string[] $files
 */

 $filesText = $files ? implode("\n", $files) : null;

$cats[null] = CategoryDTO::Empty();
$cats[null]->name = "Others";

foreach($cats as $key=>$cat){
	$cats[$key]->tags = array();
}
foreach($tags as $tag){
	$cats[$tag->categoryId]->tags[] = $tag;
}
?>

<div>
	<form action="<?=value($action)?>" method="post">
		<label for="slug">Slug</label> 
		<input id="slug" name="slug" type="text" value="<?=$art->slug?>"/>
		
		<br/>
		
		<label for="title">Title</label> 
		<input id="title" name="title" type="text" value="<?=$art->title?>"/>

		<br/>

		<label for="date">Date</label> 
		<input id="date" name="date" type="date"  value="<?=$art->date?>"/>

		<br/>
		
		<label for="description">Descriptions</label> <br/>
		<textarea id="description" name="description"><?=$art->description?></textarea>

		<br/>

		<h4><label for="files">Files :</label></h4>
		<textarea id="files" name="files"><?=$filesText?></textarea>

		<br/>

		<h4>Tags :<h4>
		<?php
		foreach($cats as $cat){
			$name = $cat->GetName();
			$style = $cat->color ? "style=\"color: $cat->color\"" : null;
			print("<h5 $style>♦ $name<h5>");
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

		<br/>

		<input type="submit" value="Submit"/>
	</form>
</div>