<div class="header">
	<h1><?=ArtArchive::GetSiteName()?></h1>
	<div class="shortcuts">
		<a href=<?=URL::Home()?>>home</a>
		 | 
		<a href="<?=URL::About()?>">About</a>
		 | 
		<a href=<?=URL::Search()?>>Search</a>
		 | 
		<a href="<?=URL::TagsHome()?>">Tags and Categories</a>
		<?php
		if (!ArtArchive::$isWebmaster){
			?>
			| 
		   <a href="<?=URL::Login()?>">Login</a>
			<?php
		}
		?>
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
		</div>
		<?php
	}
	?>
</div>
<hr/>