<?php
require_once ".settings.php";

$page = new PageBuilder("Site settings");
$page->StartPage();
	?>

	<form method="POST">
		
		<h2 id=SiteInfoSettings>Header</h2>
		<label for="SiteName">Site name</label>
		<input id="SiteName" type="text" name="settings[SiteName]" placeholder="ArtArchive" value="<?=htmlspecialchars($settings["SiteName"])?>" />

		<label for="SiteLogo">Site logo</label>
		<input id="SiteLogo" type="text" name="settings[SiteLogo]" placeholder="/resources/logo.png" value="<?=htmlspecialchars($settings["SiteLogo"])?>" />

		<br/>

		<label for=socialLinks>Social Links</label>
		<textarea id="socialLinks" name="configs[socialLinks]"
			placeholder="[My Tumblr](https://my.tumblr.com)"
		><?=$configs['socialLinks']?></textarea>

		<br/>

		<h2 id=RssSettings>RSS infos</h2>
		<label for="AuthorName">Author Name</label>
		<input id="AuthorName" type="text" name="settings[AuthorName]" value="<?=htmlspecialchars($settings["AuthorName"])?>" />
		<br/>
		<label for="AutorEmail">Author Email</label>
		<input id="AutorEmail" type="text" name="settings[AuthorEmail]" value="<?=htmlspecialchars($settings["AuthorEmail"])?>" />
		<p>
			<i>Author infos are used in the RSS feeds, and thus are made public. Both are optional.</i>
		</p>

		<input type="submit"/>
	</form>

	<?php
$page->EndPage();
?>
