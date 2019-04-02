<div>
	<form action="<?=value($action)?>" method="post">
		<label for="slug">Slug</label>
		<input id="slug" name="slug" type="text" value="<?=$cat->slug?>"/>

		<br/>

		<label for="name">Name</label>
		<input id="name" name="name" type="text" value="<?=$cat->name?>"/>

		<br/>

		<label for="color">Color</label>
		<input id="color" name="color" type="color" value="<?=$cat->color?>"/>

		<br/>
		
		<label for="description">Descriptions</label> <br/>
		<textarea id="description" name="description"><?=$cat->description?></textarea>

		<br/>

		<input type="submit" value="Submit"/>
	</form>
</div>