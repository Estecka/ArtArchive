<?php
require("../../ArtArchive.php");

$slug = value($_GET['category']);

if (empty($slug)){
	PageBuilder::ErrorDocument(400);
	die;
}

$bdd = &ArtArchive::$database;
/** @var CategoryDTO */
$cat = $bdd->GetCategoryBySlug($slug);

if ($cat == null) {
	PageBuilder::ErrorDocument(404);
	die;
}

$name = $cat->GetName();

$style = $cat->color ? "style=\"color : $cat->color\"" : null;

$page = new PageBuilder();
$page->title = "Category : ".$name;
$page->StartPage();
	if (ArtArchive::$isWebmaster) {
		?>
		<a href="edit.php?category=<?=$slug?>">Edit</a>
		|
		<a href="delete.php?category=<?=$slug?>">Delete</a>
		<?php
	}
	?>
	<h2 <?=$style?>><?=$name?></h2>
	<?php
	print($cat->description ?? "This category has no description.");
$page->EndPage();

?>