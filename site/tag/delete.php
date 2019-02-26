<?php
require("../../ArtArchive.php");

$slug = value($_GET['tag']);
$confirmation = value($_POST['slug']);

if ($slug == null){
	PageBuilder::ErrorDocument(400, "No slug");
	die;
} else if (empty($confirmation)){
	?>
	Are you sure you want to delete "<?=$slug?>" ?
	<form action="" method="post">
		<input type="hidden" name="slug" value="<?=$slug?>"/>
		<input type = "submit" value = "Yes, I do"/>
	</form>
	<?php
} else if ($confirmation != $slug) {
	PageBuilder::ErrorDocument(400, "Wrong slug");
	die;
} else {
	$page = new PageBuilder();
	$bdd = new DBService();
	$result = $bdd->DeleteTag($slug);
	if ($result) {
		$page->StartPage();
			print("Tag deleted");
		$page->EndPage();
	}
	else {
		PageBuilder::ErrorDocument(404);
		die;
	}
}
?>