
<?php
	require("../database/DBService.php");
	$bdd = new DBService();

	$artworks = $bdd->GetArtworks();
?>
<!DOCTYPE html>
<html>
	<?php include ("../templates/html-head.php") ?>
	<body>
		<?php include("../templates/header.php") ?>

		<?php
			foreach($artworks as $art)
				include("../templates/ArtCard.php");
		?>
	</body>
</html>