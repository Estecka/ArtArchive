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
	<h2>Site config</h2>

	<form method="POST">
		<label for="name">Site name</label>
		<input id="name" type="text" name="settings[SiteName]" placeholder="ArtArchive" value="<?=htmlspecialchars($settings["SiteName"])?>" />

		<br/>
		<br/>

		<label for="name">Author Name</label>
		<input id="name" type="text" name="settings[AuthorName]" value="<?=htmlspecialchars($settings["AuthorName"])?>" />
		<br/>
		<label for="name">Author Email</label>
		<input id="name" type="text" name="settings[AuthorEmail]" value="<?=htmlspecialchars($settings["AuthorEmail"])?>" />
		<p>
			<i>Author infos are used in the RSS feeds, and thus are made public. Both are optional.</i>
		</p>

		<br/>

		<label for="rpp">Results per page</label>
		<input id="rpp" type=number name="settings[ResultsPerPage]" placeholder=20 value="<?=(int)$settings["ResultsPerPage"]?>" />

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