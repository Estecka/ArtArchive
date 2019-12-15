<?php
require("../../ArtArchive.php");
ArtArchive::RequireWebmaster();

$bdd = &ArtArchive::$database;

if (!empty($_POST)) {
	try {
		$bdd->StartTransaction();
		$bdd->SetSettings($_POST['settings']);
		$bdd->SetPage("about", $_POST['pages']['about']);
		$bdd->CommitTransaction();
	} catch (PDOException $e){
		$bdd->Rollback();
		echo $e->getCode();
		echo "<br/>";
		echo $e->getMessage();
		die;
	}
}


$settings = &ArtArchive::$settings;

try {
	$settings = $bdd->GetSettings($settings);
	$about = $bdd->GetPage("about");
} catch (PDOException $e) {
	echo $e->getCode();
	echo "<br/>";
	echo $e->getMessage();
	die;
}


$page = new PageBuilder();
$page->title = "Site settings";
$page->StartPage();
	?>

	<form method="POST">
		
		<h2>Site infos</h2>
		<label for="name">Site name</label>
		<input id="name" type="text" name="settings[SiteName]" placeholder="ArtArchive" value="<?=htmlspecialchars($settings["SiteName"])?>" />

		<br/>

		<label for="about">About page</label>
		<br/>
		<textarea name="pages[about]" id ="about"><?=htmlspecialchars($about)?></textarea>

		<br/>

		<h2>RSS infos</h2>
		<label for="name">Author Name</label>
		<input id="name" type="text" name="settings[AuthorName]" value="<?=htmlspecialchars($settings["AuthorName"])?>" />
		<br/>
		<label for="name">Author Email</label>
		<input id="name" type="text" name="settings[AuthorEmail]" value="<?=htmlspecialchars($settings["AuthorEmail"])?>" />
		<p>
			<i>Author infos are used in the RSS feeds, and thus are made public. Both are optional.</i>
		</p>

		<br/>

		<h2>Site layout</h2>
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