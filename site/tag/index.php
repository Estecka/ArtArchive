<?php
require("../../ArtArchive.php");

$slug = value($_GET['tag']);

if (empty($slug)){
	PageBuilder::ErrorDocument(400);
	die;
}

$bdd = &ArtArchive::$database;
/** @var TagDTO **/
$tag = $bdd->GetTag($slug);
if ($tag == null){
	PageBuilder::ErrorDocument(404);
	die;
}
$name = $tag->GetName();

$rpp = ArtArchive::$settings["ResultsPerPage"];
$currentPage = either($_GET['page'], 0);
$artworks = $bdd->SearchArtworks(array($tag->id), $rpp, $currentPage, $total);

if (isset($_GET['feed_xml'])){
	require ("../../templates/RSSBuilder.php");
	$rss = new RSSBuilder();
	$rss->title = "Tag : ".$name;
	$rss->link = URL::Tag($slug);
	$rss->description = "All artworks tagged with ".$slug;
	$rss->Init();
	foreach($artworks as $art)
		$rss->AddArtwork($art);
	$rss->Flush();
	exit;
}

if ($artworks){
	$artworks = $bdd->GetThumbnails($artworks);
	$pageAmount = (int)ceil($total /$rpp);
}

$page = new PageBuilder();
$page->title = $name;
$page->rssfeeds["#".$slug] = "feed.xml";
$page->StartPage();
	if (ArtArchive::$isWebmaster)
	{
		?>
		<a href="<?=URL::EditTag($slug)?>">Edit</a>
		 | 
		<a href="<?=URL::DeleteTag($slug)?>">Delete</a>
		<?php
	}
	print("<h1>$name</h1>");
	if ($slug != $name)
		print("<h4>$slug</h4>");

	if ($tag->description)
		print(str_replace("\n", "<br/>", $tag->description));
	else
		print("This tag has no description.");
	
	if ($artworks){
		print "<h3>Related artworks : </h3>";
		$page->ArtCardList($artworks);
		$page->PageList("?page=%d", $currentPage, $pageAmount);
	}
$page->EndPage();
?>