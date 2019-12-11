<?php
require("../ArtArchive.php");
$bdd = &ArtArchive::$database;

$rpp = ArtArchive::$settings['ResultsPerPage'];
$pageNo = either($_GET["page"], 0);
$artworks = $bdd->GetArtworks($rpp, $pageNo, $total);

if (isset($_GET["feed_xml"])){
	?>boop<?php
	exit;
}

if ($artworks)
	$artworks = $bdd->GetThumbnails($artworks);

$page = new PageBuilder();
$page->title = ArtArchive::GetSiteName();
$page->rssfeeds = array("feed.xml");
$page->StartPage();

	$page->ArtCardList($artworks);
	if ($total > 10){
		$pageAmount = (int)ceil($total /$rpp);
		$page->PageList(URL::Home()."?page=%d", $pageNo, $pageAmount, 11);
	}

$page->EndPage();
?>
