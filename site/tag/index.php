<?php
require("../../ArtArchive.php");

$slug = value($_GET['tag']);

if (empty($slug))
	http_response_code(400);
else {
	$bdd = new DBService();
	/** @var TagDTO **/
	$tag = $bdd->GetTag($slug);
	
	if ($tag == null)
		http_response_code(404);
}

$code = http_response_code();
$name = $tag->GetName();
$page = new PageBuilder();

if ($code == 200)
	$page->title = $name;
else
	$page->title = $code;

$page->StartPage();
	if ($code != 200)
		print("<h1>$code</h1>");
	else {
		print("<h1>$name</h1>");
		if ($slug != $name)
			print("<h3>$slug</h3>");
		
		if ($tag->description)
			print($tag->description);
		else
			print("This tag has no description.");
	}
$page->EndPage();
?>