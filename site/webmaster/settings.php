<?php
require("../../ArtArchive.php");
ArtArchive::RequireWebmaster();

$bdd = &ArtArchive::$database;

if (!empty($_POST)) {
	try {
		$bdd->SetSettings($_POST);
	} catch (PDOException $e){
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
		<input id ="name" type="text" name="SiteName" placeholder="MyArtDump" value="<?=htmlspecialchars($settings["SiteName"])?>" />

		<br/>
		<input type="submit"/>
	</form>

	<?php
$page->EndPage();
?>