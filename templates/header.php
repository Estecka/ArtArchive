<?php
/**
 * @var PageBuilder $this
*/

$links = ArtArchive::$database->GetPage("socialLinks");
$links = trim($links);
?>

<header>
	<nav id="Feeds">
		<?php
		foreach ($this->rssfeeds as $name => $url){
			?>
			<a href="<?=$url?>" class="largeSocial" title="RSS Feed">
				<div>
					<span><?=$name?></span>
					<img src="/resources/rss-32x32.png"/>
				</div>
			</a>
			<?php
		}
		?>
	</nav>

	<img id="MainLogo" src="<?=ArtArchive::$settings["SiteLogo"]?>" />

	<h1><?=ArtArchive::GetSiteName()?></h1>
	<nav class='shortcuts'>
		<a class='iconLink home' href=<?=URL::Home()?>>Home</a>
		 | 
		<a class='iconLink info' href="<?=URL::About()?>">About</a>
		 | 
		<a class='iconLink search' href=<?=URL::Search()?>>Search</a>
		 | 
		<a class='iconLink tags' href="<?=URL::TagsHome()?>">Tags and Categories</a>
		<?php
		if (!ArtArchive::$isWebmaster){
			?>
			| 
		   <a href="<?=URL::Login()?>">Login</a>
			<?php
		}
		?>
	</nav>
	<?php
		if (!empty($links)){
			?>
			<hr/>
			<nav class=socials>
				<?php $this->LinkList($links); ?>
			</nav>
			<?php
		}
		?>
	<?php 
	if (ArtArchive::$isWebmaster) {
		?>
		<hr/>
		<nav class=webmaster>
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
	</nav>
		<?php
	}
	?>
</header>