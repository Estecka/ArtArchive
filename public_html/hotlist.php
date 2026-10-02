<?php
require("../ArtArchive.php");
require_once __ROOT__."/php/Markdown.php";

if ($_POST){
	if (!isset($_POST['list'])){
		PageBuilder::ErrorDocument(400, "Empty List");
		die;
	}

	$list = explode("\n", $_POST['list']);
	foreach ($list as $i=>$v){
		$v = trim($v);
		$v = preg_replace("'(https?://)?(.*)'", "$2", $v);
		$root = $_SERVER['SERVER_NAME']."/art/";
		if (startsWith($v, $root)){
			$v = substr($v, strlen($root));
			$v = preg_replace("'([a-zA-Z0-9_-]+)(/.*)?'", "$1", $v);
		}

		$list[$i] = $v;
	}

	$list = implode("+", $list);
	header("Location:/hotlist.php?list=".$list, false, 303);
	exit;
}

$list = value($_GET['list']);
$list = explode(" ", $list);

$bdd = &ArtArchive::$database;
try {
	$artworks = $bdd->GetArtworksBySlug($list);
}
catch (PDOException $e){
	PageBuilder::ErrorDocumentDebug(500, $e->getMessage());
	die;
}

if (!$artworks && !ArtArchive::$isWebmaster){
	PageBuilder::ErrorDocument(404, "Empty list", "<i class=emptyNotice>No artworks were found</i>");
	die;
}

if ($artworks) {
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

	if (!empty($orderedArtworks))
		$page->ArtCardList($orderedArtworks);

	if (ArtArchive::$isWebmaster){
		?>
		<form method='POST'>
			<textarea
				name='list'
				placeholder="One slug or URL per line"
			><?=implode("\n", $list)?></textarea>
			<input type=submit />
		</form>
		<?php
	}

	?></article><?php
$page->EndPage();
?>