<div class="header">
	<h1><?=ArtArchive::GetSiteName()?></h1>
	<div class="shortcuts">
		<a href=<?=URL::Home()?>>home</a>
		 | 
		<a href=<?=URL::Search()?>>Search</a>
		 | 
		<a href="<?=URL::TagsHome()?>">Tags</a>
	</div>
	<?php 
	if (ArtArchive::$isWebmaster) {
		?>
		<hr/>
		<div>
			<b>Webmaster tools :</b>
			<a href="<?=URL::SiteSettings()?>">Control panel</a>
			| 
			<a href="<?=URL::SubmitArt()?>">Create artwork</a>
			| 
			<a href="<?=URL::SubmitTag()?>">Create tag</a>
			| 
			<a href="<?=URL::SubmitCategory()?>">Create category</a>
			| 
			<a href="<?=URL::OrderCategory()?>">Reorder Category</a>
			| 
			<a href="<?=URL::Logout()?>">Logout</a>
		</div>
		<?php
	}
	?>
</div>
<hr/>