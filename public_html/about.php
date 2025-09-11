<?php
require_once("../ArtArchive.php");
require_once __ROOT__."/templates/Markdown.php";

$bdd = &ArtArchive::$database;

$text = $bdd->GetPage("about");

$page = new PageBuilder();
$page->StartPage();
	?>
	<article class='about'>
		<?=Markdown::MarkdownToHtml($text)?>
	</article>
	<?php
$page->EndPage();
?>
