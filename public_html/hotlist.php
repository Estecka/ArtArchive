<?php
require("../ArtArchive.php");
require_once __ROOT__."/php/Markdown.php";

$list = value($_GET['list']);
$list = explode(" ", $list);

if (empty($list)){
	PageBuilder::ErrorDocument(400);
	die;
}

$bdd = &ArtArchive::$database;
try {
	$artworks = $bdd->GetArtworksBySlug($list);
}
catch (PDOException $e){
	PageBuilder::ErrorDocumentDebug(500, $e->getMessage());
	die;
}

if (!$artworks){
	PageBuilder::ErrorDocument(404, "Empty list", "<i class=emptyNotice>No artworks were found</i>");
	die;
}

$artworks = $bdd->GetThumbnails($artworks);

$missing = array();
$orderedArtworks = array();
foreach ($list as $slug)
if (!empty($slug)) {
	if (empty($artworks[$slug]))
		$missing[] = $slug;
	else
		$orderedArtworks[] = $artworks[$slug];
}


$page = new PageBuilder("Hot List");
$page->StartPage();
	?><article><?php
	if (!empty($missing)) {
		?>
		<p><i class=emptyNotice>
			The following artworks were not found:<br/> <?=htmlspecialchars(implode(", ", $missing))?>
		</i></p>
		<?php
	}

	$page->ArtCardList($orderedArtworks);
	// $page->PageList("?page=%d", $currentPage, $pageAmount);
	?></article><?php
$page->EndPage();
?>