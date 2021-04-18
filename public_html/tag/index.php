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

$page = new PageBuilder($name);
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

	?>
	<a href="feed.xml" class="social" title="Tagged : <?=$slug?>">
		<h4>
			<img src="/resources/rss-32x32.png"/>
			<span><?=$slug?></span>
		</h4>
	</a>

	<p>
		<?php
		if ($tag->description)
			print(str_replace("\n", "<br/>", $tag->description));
		else
			print("This tag has no description.");
		?>
	</p>
	<?php
	
	if ($artworks){
		print "<h3>Related artworks : </h3>";
		$page->ArtCardList($artworks);
		$page->PageList("?page=%d", $currentPage, $pageAmount);
	}
$page->EndPage();
?>