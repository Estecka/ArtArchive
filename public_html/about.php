<?php
require("../ArtArchive.php");
$bdd = &ArtArchive::$database;

$text = $bdd->GetPage("about");

$page = new PageBuilder();
$page->title = ArtArchive::GetSiteName();
$page->StartPage();
	
	print "<div>$text</div>";

$page->EndPage();
?>
