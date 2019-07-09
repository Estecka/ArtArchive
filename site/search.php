<?php
require("../ArtArchive.php");

if ($_POST){
	$tags = array();
	if (isset($_POST["add"]))
		foreach($_POST["add"] as $key=>$value)
			$tags[] = $key;
	if (isset($_POST["keep"]))
		foreach($_POST["keep"] as $key=>$value)
			$tags[] = $key;

	$tags = implode("+", $tags);
	header("Location:".URL::Search($tags), false, 303);
	exit;
}

$bdd = &ArtArchive::$database;
$allTags = $bdd->GetAllTags();
$allCats = $bdd->GetAllCategories();

$tags = either($_GET["tags"], null);
$pageNo = either($_GET["page"], 0);
$enabledTags = array();

if ($tags !== null){
	$tags = explode(" ", $tags);

	$searchIds = $bdd->TagSlugsToID($tags);
	$invalidSlugs = array();
	$validIds = array();
	foreach($searchIds as $slug=>$id) {
		if ($id == null)
		$invalidSlugs[] = $slug;
		else{
			$validIds[] = $id;
			$enabledTags[$slug] = true;
		}
	}


	$arts = $bdd->SearchArtworks($validIds, 10, $pageNo, $total);
	if ($arts)
		$arts = $bdd->GetThumbnails($arts);
	$pageAmount = (int)ceil($total * 0.1);
}

foreach($allTags as $key=>$tag)
	$allTags[$key]->enabled = either($enabledTags[strtolower($tag->slug)], false);


$page = new PageBuilder();
$page->title = "Search";
$page->StartPage();
	if (isset($arts)){
		if (!empty($invalidSlugs))
			print("The following tags do not exist and were ignored : \n".implode(", ", $invalidSlugs));

		print("<h2>Results : </h2>");
		if (sizeof($arts) <= 0)
			print("This search did not yield any results. :(");
		else
		foreach($arts as $art){
			$page->ArtCard($art);
		}
		$page->PageList(URL::Search($_GET["tags"], "%d"), $pageNo, $pageAmount, 11);
	}
	?>
	<form method="POST">
		<?php
		$page->TagSelectionForm($allTags, $allCats, false);
		?>
		<input type="reset"/>
		<input type="submit"/>
	</form>
	<?php
$page->EndPage();
?>