<div>
	<form action="" method="post">
		<label for="slug">Slug</label> 
		<input id="slug" name="slug" type="text" value="<?=value($_POST['slug'])?>"/>
		
		<br/>
		
		<label for="title">Title</label> 
		<input id="title" name="title" type="text" value="<?=value($_POST['title'])?>"/>

		<br/>

		<label for="date">Date</label> 
		<input id="date" name="date" type="date"  value="<?=value($_POST['date'])?>"/>

		<br/>
		
		<label for="description">Descriptions</label> <br/>
		<textarea id="description" name="description"><?=value($_POST['description'])?></textarea>

		<br/>
		
		<input type="submit" value="Create Draft"/>
	</form>
</div>