<?php
require("../../ArtArchive.php");
require_once __ROOT__."/php/Markdown.php";

$slug = value($_GET['category']);

if (empty($slug)){
	PageBuilder::ErrorDocument(400, "Bad request");
	die;
}

$bdd = &ArtArchive::$database;
/** @var CategoryDTO */
$cat = $bdd->GetCategoryBySlug($slug);
if ($cat == null) {
	PageBuilder::ErrorDocument(404, "Category not found");
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
		<nav class='webmaster categoryControls'>
			<a href="<?=URL::EditCategory($slug)?>">Edit</a>
			 |
			<a href="<?=URL::DeleteCategory($slug)?>" class=danger>Delete</a>
		</nav>
		<?php
	}
	?>
	<article>
		<h1 id=CategoryName <?=$style?>><?=$name?></h1>
		<?php
		if ($cat->description) {
			print(Markdown::MarkdownToHtml($cat->description));
		}
		else if (!$tags)
		{
			?><p class='emptyNotice'><i>This category is empty.</i></p><?php
		}

		?>
		<!-- <h3>Tags:</h3> -->
		<ul class='tagList extendedTagList' style="--cat-color:<?=$cat->color?>">
			<?php
			foreach($tags as $t){
				?><li class='tagName'><?php $page->TagPreview($t) ?></li><?php
			}
			?>
		</ul>
	</article>
	<?php

$page->EndPage();

?>