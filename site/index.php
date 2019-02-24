<?php
require("../Artarchive.php");
$bdd = new DBService();

$artworks = $bdd->GetArtworks();

$page = new PageBuilder();
$page->StartPage();

	foreach($artworks as $art)
		$page->ArtCard($art);

$page->EndPage();
?>
