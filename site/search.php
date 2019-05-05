<?php
require("../Artarchive.php");
$bdd = new DBService();

$tags = value($_GET["tags"]);
$page = either($_GET["page"], 0);

var_dump($tags);
if ($tags !== false){
	$tags = explode(" ", $tags);
	var_dump($tags);
	$arts = $bdd->GetArtworksByTags($tags, 100);
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