<?php
require("../../ArtArchive.php");

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
	$bdd = new DBService();
	$result = $bdd->DeleteArtwork($slug);
	if ($result)
		header("Location:".URL::Home());
	else {
		http_response_code(404);
		print (404);
		die;
	}
}
?>