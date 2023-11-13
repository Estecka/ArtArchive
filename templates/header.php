<?php
/**
 * @var PageBuilder $this
*/

$links = ArtArchive::$database->GetPage("socialLinks");
$links = trim($links);
?>

<div id=Header>
	<div id="Feeds">
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
	</div>

	<img id="MainLogo" src="<?=ArtArchive::$settings["SiteLogo"]?>" />

	<h1><?=ArtArchive::GetSiteName()?></h1>
	<div class="shortcuts">
		<a href=<?=URL::Home()?>>Home</a>
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
		if (!empty($links)){
			?>
			<hr/>
			<div class=extLinkList>
				<?php $this->LinkList($links); ?>
			</div>
			<?php
		}
		?>
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