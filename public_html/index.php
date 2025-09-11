<?php
require("../ArtArchive.php");
require_once __ROOT__."/templates/Markdown.php";
$bdd = &ArtArchive::$database;

$rpp = ArtArchive::$settings['ResultsPerPage'];
$pageNo = either($_GET["page"], 0);
$artworks = $bdd->GetArtworks($rpp, $pageNo, $total);
$homePage = $bdd->GetPage("home");

if (isset($_GET["feed_xml"])){
	require __ROOT__."/templates/RSSBuilder.php";
	$rss = new RSSBuilder();
	$rss->title = "All Artworks";
	$rss->link = URL::Home();
	$rss->description = "Feed for every artworks that are posted in this gallery";
	$rss->Init();
	foreach($artworks as $art)
		$rss->AddArtwork($art);
	$rss->Flush();
	exit;
}

if ($artworks)
	$artworks = $bdd->GetThumbnails($artworks);

$page = new PageBuilder();
$page->StartPage();

	?>
	<section class="homePage">
		<?=Markdown::MarkdownToHtml($homePage)?>
	</section>
	<?php

	$page->ArtCardList($artworks);
	if ($total > 10){
		$pageAmount = (int)ceil($total /$rpp);
		$page->PageList(URL::Home()."?page=%d", $pageNo, $pageAmount, 10);
	}

$page->EndPage();
?>
