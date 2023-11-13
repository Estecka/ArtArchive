<?php
require("../../ArtArchive.php");
require_once __ROOT__."/templates/Markdown.php";

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

/** @var ?CategoryDTO */
if ($tag->categoryId){
	$cat = $bdd->GetCategoryById($tag->categoryId);
	$catColorStyle = "style='--cat-color:$cat->color'";
}
else{
	$cat = null;
	$catColorStyle = null;
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
$page->rssfeeds["#".$slug] = URL::Tag($slug)."feed.xml";
$page->StartPage();
	if (ArtArchive::$isWebmaster)
	{
		?>
		<a href="<?=URL::EditTag($slug)?>">Edit</a>
		 | 
		<a href="<?=URL::DeleteTag($slug)?>">Delete</a>
		<?php
	}
	?>

	<!-- <div id="Feeds">
		<a id=Feeds href="feed.xml" class="social" title="Tagged : <?=$slug?>">
			<h4>
				<img src="/resources/rss-32x32.png"/>
				<span><?=$slug?></span>
			</h4>
		</a>
	</div> -->

	<span id=TagTitles <?=$catColorStyle?>>
		<h1 id=TagMaintitle><?=$name?></h1>
		<?php
	if ($cat){
		?>
		<h2 id=TagSubtitle>
			<a href="<?=URL::Category($cat->slug)?>"><?=$cat->GetName()?></a>
		</h2>
		<?php
	}
	?>
	</span>

	<p>
		<?php
		if ($tag->description)
			print(Markdown::MarkdownToHtml($tag->description));
		else
			print("This tag has no description.");
		?>
	</p>
	<?php
	
	if ($artworks){
		// print "<h3>Related artworks : </h3>";
		$page->ArtCardList($artworks);
		$page->PageList("?page=%d", $currentPage, $pageAmount);
	}
$page->EndPage();
?>