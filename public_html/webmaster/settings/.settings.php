<?php
require_once "../../../ArtArchive.php";
ArtArchive::RequireWebmaster();

$bdd = &ArtArchive::$database;

if (!empty($_POST)) {
	try {
		$bdd->StartTransaction();
		if (!empty($_POST['settings'])) $bdd->SetSettings($_POST['settings']);
		if (!empty($_POST['configs']))  $bdd->SetConfigs ($_POST['configs']);
		if (!empty($_POST['pages']))    $bdd->SetPages   ($_POST['pages']);
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
$pages    = ArtArchive::$database->GetPages(); // TODO: Default values
$configs  = &$pages;

// Refresh the site's settings after they have been updated by the POST
try {
	$settings = $bdd->GetSettings($settings);
} catch (PDOException $e) {
	PageBuilder::ErrorDocument(500, "<h2>".$e->getCode()."</h2>".$e->getMessage());
	die;
}
?>
