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
		<nav class='webmaster tagControls'>
			<a href="<?=URL::EditTag($slug)?>">Edit</a>
			 | 
			<a href="<?=URL::DeleteTag($slug)?>" class='danger'>Delete</a>
		</nav>
		<?php
	}
	?>

	<article class='tagPage'>
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

		<?php
		if ($tag->description)
			print(Markdown::MarkdownToHtml($tag->description));
		else if (!$artworks) {
			?><p><i class='emptyNotice'>This tag is empty<i></p> <?php
		}
		?>
	</article>
	<?php
	
	if ($artworks){
		// print "<h3>Related artworks : </h3>";
		$page->ArtCardList($artworks);
		$page->PageList("?page=%d", $currentPage, $pageAmount);
	}
$page->EndPage();
?>