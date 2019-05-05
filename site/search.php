<?php
require("../Artarchive.php");
$bdd = new DBService();

$tags = value($_GET["tags"]);
$page = either($_GET["page"], 0);

if ($tags !== false){
	$tags = explode(" ", $tags);

	$searchIds = $bdd->TagSlugsToID($tags);
	$invalidSlugs = array();
	$validIds = array();
	foreach($searchIds as $slug=>$id)
		if ($id == null)
			$invalidSlugs[] = $slug;
		else
			$validIds[] = $id;


	$arts = $bdd->GetArtworksByTags($validIds, 100);
}



$page = new PageBuilder();
$page->title = "Search";
$page->StartPage();
	?>
	<form method="GET">
		<input type="text" placeholder="tags" name="tags" value="<?=$_GET["tags"]?>"/>
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