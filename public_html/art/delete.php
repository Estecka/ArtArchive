<?php
require("../../ArtArchive.php");
ArtArchive::RequireWebmaster();

$slug = value($_GET['art']);
$confirmation = value($_POST['slug']);

if ($slug == null){
	http_response_code(400);
	print("400 - No slug");
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
	http_response_code(400);
	print("400 - Wrong slug");
	exit;
} else {
	$page = new PageBuilder();
	$bdd = &ArtArchive::$database;
	$result = $bdd->DeleteArtwork($slug);
	if ($result) {
		$page->StartPage();
		print("Artwork deleted");
		$page->EndPage();
	}
	else {
		http_response_code(404);
		print (404);
		die;
	}
}
?>