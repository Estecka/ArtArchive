<?php
require("../../ArtArchive.php");

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

$name = $tag->GetName();

$page = new PageBuilder();
$page->title = $name;
$page->StartPage();
	if (ArtArchive::$isWebmaster)
	{
		?>
		<a href="<?=URL::EditTag($slug)?>">Edit</a>
		 | 
		<a href="<?=URL::DeleteTag($slug)?>">Delete</a>
		<?php
	}
	print("<h1>$name</h1>");
	if ($slug != $name)
		print("<h3>$slug</h3>");

	if ($tag->description)
		print($tag->description);
	else
		print("This tag has no description.");
$page->EndPage();
?>