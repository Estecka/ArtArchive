<?php
require("../ArtArchive.php");
$bdd = &ArtArchive::$database;

$rpp = ArtArchive::$settings['ResultsPerPage'];
$pageNo = either($_GET["page"], 0);
$artworks = $bdd->GetArtworks($rpp, $pageNo, $total);

if (isset($_GET["feed_xml"])){
	require __ROOT__."/templates/RSSBuilder.php";
	$rss = new RSSBuilder();
	$rss->title = "All Artworks";
	$rss->link = URL::Home();
	$rss->description = "Feed for every artworks that are posted in this gallery";
	$rss->Init();
	$rss->Flush();
	exit;
}

if ($artworks)
	$artworks = $bdd->GetThumbnails($artworks);

$page = new PageBuilder();
$page->title = ArtArchive::GetSiteName();
$page->rssfeeds = array("All Artworks" => "/feed.xml");
$page->StartPage();

	$page->ArtCardList($artworks);
	if ($total > 10){
		$pageAmount = (int)ceil($total /$rpp);
		$page->PageList(URL::Home()."?page=%d", $pageNo, $pageAmount, 11);
	}

$page->EndPage();
?>
