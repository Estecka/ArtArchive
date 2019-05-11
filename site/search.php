<?php
require("../Artarchive.php");

$bdd = new DBService();
$allTags = $bdd->GetAllTags();
$allCats = $bdd->GetAllCategories();

$tags = either($_GET["tags"], null);
$page = either($_GET["page"], 0);
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


	$arts = $bdd->GetArtworksByTags($validIds, 100);
}

foreach($allTags as $key=>$tag)
	$allTags[$key]->enabled = either($enabledTags[$tag->slug], false);


$page = new PageBuilder();
$page->title = "Search";
$page->StartPage();
	?>
	<form method="GET">
		<input type="text" placeholder="tags" name="tags" value="<?=$_GET["tags"]?>"/>
		<input type="submit"/>
	</form>
	<form method="POST">
		<?php
		$page->TagSelectionForm($allTags, $allCats, false);
		?>
		<input type="submit"/>
	</form>
	<?php
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
	}
$page->EndPage();
?>