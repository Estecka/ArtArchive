<?php
require_once("../ArtArchive.php");
require_once __ROOT__."/templates/Markdown.php";

$bdd = &ArtArchive::$database;

$text = $bdd->GetPage("about");
$text = Markdown::MarkdownToHtml($text);

$page = new PageBuilder();
$page->StartPage();
	
	print "<div>$text</div>";

$page->EndPage();
?>
