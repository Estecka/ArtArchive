<?php
require_once("../Artarchive.php");
ArtArchive::RequireWebmaster();
$bdd = &ArtArchive::$database;

/**
 * @var CategoryDTO[] $cats
 */
$cats = $bdd->GetAllCategories();

$page = new PageBuilder();
$page->title = "Categories order";
$page->StartPage();

	print("<h2>Categories order</h2>");

	?>
	<form method="post" action="reordercats.php"> 
		<?php
		$i = 0;
		foreach($cats as $cat){
			?>
			<input id="<?=$cat->slug?>" name="<?=$cat->slug?>" type="number" value="<?=$i?>"/>
			<label for="<?=$cat->slug?>"><?=$cat->GetName()?></label>
			<br/>
			<?php
			$i++;
		}
		?>

		<input type="submit" value="Reorder"/>
	</form>
	<?php

$page->EndPage();
?>