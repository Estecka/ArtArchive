<?php
require("../../ArtArchive.php");
require_once __ROOT__."/templates/Markdown.php";

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

$tags = $bdd->GetTagsFromCategory($cat->id);

$name = $cat->GetName();

$style = $cat->color ? "style='--cat-color:$cat->color'" : null;

$page = new PageBuilder("Category : $name");
$page->StartPage();
	if (ArtArchive::$isWebmaster)
	{
		?>
		<a href="<?=URL::EditCategory($slug)?>">Edit</a>
		 |
		<a href="<?=URL::DeleteCategory($slug)?>">Delete</a>
		<?php
	}
	?>
	<h1 id=CategoryName <?=$style?>><?=$name?></h1>
	<?php
	print(Markdown::MarkdownToHtml($cat->description ?? "This category has no description."));

	?>
	<!-- <h3>Tags:</h3> -->
	<ul class=extendedTagList style="--cat-color:<?=$cat->color?>">
		<?php
		foreach($tags as $t){
			?><li><?php $page->TagPreview($t) ?></li><?php
		}
		?>
	</ul>
	<?php

$page->EndPage();

?>