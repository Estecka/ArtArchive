<?php
require("../../ArtArchive.php");

$slug = value($_GET['art']);

if (empty($slug))
	http_response_code(400);
else {
	$bdd = new DBService();
	/** @var ArtworkDTO **/
	$art = $bdd->GetArtwork($slug);
	
	if ($art == null)
		http_response_code(404);
}

$code = http_response_code();
$name = $art->title ?? $slug;
$page = new PageBuilder();

if ($code == 200)
	$page->title = $name;
else
	$page->title = $code;

$page->StartPage();
	if ($code != 200)
		print("<h1>$code</h1>");
	else {
		PageBuilder::ArtForm($art);
	}
$page->EndPage();

?>