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


$settings = array();
$settings["SiteName"] = "MyArtDump";

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
	<h2>Site config</h2>

	<form method="POST">
		<label for="name">Site name</label>
		<input id="name" type="text" name="settings[SiteName]" placeholder="MyArtDump" value="<?=htmlspecialchars($settings["SiteName"])?>" />

		<br/>

		<label for="about">About page</label>
		<br/>
		<textarea name="pages[about]" id ="about"><?=htmlspecialchars($about)?></textarea>

		<br/>

		<input type="submit"/>
	</form>

	<?php
$page->EndPage();
?>