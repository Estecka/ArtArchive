<?php
require_once "../../ArtArchive.php";
ArtArchive::RequireWebmaster();

$bdd = &ArtArchive::$database;

if (!empty($_POST)) {
	try {
		$bdd->StartTransaction();
		$bdd->SetSettings($_POST['settings']);
		$bdd->SetConfigs ($_POST['configs']);
		$bdd->SetPages   ($_POST['pages']);
		$bdd->CommitTransaction();
	} catch (PDOException $e){
		$bdd->Rollback();
		echo $e->getCode();
		echo "<br/>";
		echo $e->getMessage();
		die;
	}
}

require_once __ROOT__."/php/SocialIcon.php";

$settings = &ArtArchive::$settings;
$pages    = ArtArchive::$database->GetPages(); // TODO: Default values
$configs  = &$pages;

try {
	$settings = $bdd->GetSettings($settings);
	$about = $bdd->GetPage("about");
	$home  = $bdd->GetPage("home");
} catch (PDOException $e) {
	echo $e->getCode();
	echo "<br/>";
	echo $e->getMessage();
	die;
}


$page = new PageBuilder("Site settings");
$page->StartPage();
	?>

	<form method="POST">
		
		<h2>Site infos</h2>
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

		<label for="home">Home page</label>
		<br/>
		<textarea name="pages[home]" id="home" placeholder="Write HTML here"><?=htmlspecialchars($pages['home'])?></textarea>

		<label for="about">About page</label>
		<br/>
		<textarea name="pages[about]" id="about" placeholder="Write HTML here"><?=htmlspecialchars($pages['about'])?></textarea>

		<br/>

		<h2>RSS infos</h2>
		<label for="AuthorName">Author Name</label>
		<input id="AuthorName" type="text" name="settings[AuthorName]" value="<?=htmlspecialchars($settings["AuthorName"])?>" />
		<br/>
		<label for="AutorEmail">Author Email</label>
		<input id="AutorEmail" type="text" name="settings[AuthorEmail]" value="<?=htmlspecialchars($settings["AuthorEmail"])?>" />
		<p>
			<i>Author infos are used in the RSS feeds, and thus are made public. Both are optional.</i>
		</p>


		<h2>Site layout</h2>

		<label for=stylesheets>Stylesheet:</label>
		<input id="stylesheets" type=text name="settings[stylesheet]" 
			placeholder="<?=htmlspecialchars("/css/stylesheet.css")?>"
			value="<?=htmlspecialchars($settings['stylesheet'])?>"
		/>

		<br/>

		<label for=socialFavicons>External Link Icons</label>
		<textarea id="socialFavicons" name="configs[socialFavicons]"
			placeholder="<?=htmlspecialchars(SocialIcon::GetFormPlaceholder())?>"
		><?=value($configs['socialFavicons'])?></textarea>

		<label for="rpp">Results per page</label>
		<input id="rpp" type=number name="settings[ResultsPerPage]" placeholder=20 value="<?=(int)$settings["ResultsPerPage"]?>" />

		<br/>

		<?php
			$isLiquid = $isRigidMasonry = $isFluidMasonry = null;
			switch ($settings['tagMasonry']){
				default:
				case 0: $isLiquid       = "checked"; break;
				case 1: $isRigidMasonry = "checked"; break;
				case 2: $isFluidMasonry = "checked"; break;
			}
		?>
		<label>Tag Lists :</label>
		<div class="row">
			<div class=column>
				<input type=radio name="settings[tagMasonry]" value=1 id="Masonry" <?=$isRigidMasonry?>>
				<label for="Masonry">
					Masonry
					<br/>
					<img src="/resources/masonry.png" width=150>
				</label>
			</div>
			<div class=column>
				<input type=radio name="settings[tagMasonry]" value=2 id="FluidMasonry" <?=$isFluidMasonry?>>
				<label for="FluidMasonry">
					Liquid Masonry
					<br/>
					<img src="/resources/liquid-masonry.png" width=150>
				</label>
			</div>
			<div class=column>
				<input type=radio name="settings[tagMasonry]" value=0 id="Liquid" <?=$isLiquid?>>
				<label for="Liquid">
					Liquid Rows
					<br/>
					<img src="/resources/liquid.png" width=150>
				</label>
				<br/><label for="Liquidity">Tags per row</label>
				<br/><input id="Liquidity" type=number name="settings[tagLiquidity]" placeholder=16 value="<?=(int)$settings["tagLiquidity"]?>" />
			</div>
		</div>

		<input type="submit"/>
	</form>

	<?php
$page->EndPage();
?>