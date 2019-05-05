<?php
require("../Artarchive.php");
$bdd = new DBService();

$tags = either($_GET["tags"], false);
$page = either($_GET["page"], 0);

if ($tags){
	var_dump($tags);
	$tags = explode(" ", $tags);
	var_dump($tags);
}

$bdd->GetArtworksByTags($tags, 100);


$page = new PageBuilder();
$page->title = "Search";
$page->StartPage();
	?>
	<form method="GET">
		<input type="text" placeholder="tags" name="tags" value="<?=$_GET["tags"]?>"/>
		<input type="submit"/>
	</form>
	<?php
$page->EndPage();
?>