<div>
	<form action="<?=value($action)?>" method="post">
		<label for="slug">Slug</label> 
		<input id="slug" name="slug" type="text" value="<?=$tag->slug?>"/>
		
		<br/>
		
		<label for="name">Name</label> 
		<input id="name" name="name" type="text" value="<?=$tag->name?>"/>

		<br/>
		
		<label for="description">Descriptions</label> <br/>
		<textarea id="description" name="description"><?=$tag->description?></textarea>

		<br/>
		
		<input type="submit" value="Submit"/>
	</form>
</div>