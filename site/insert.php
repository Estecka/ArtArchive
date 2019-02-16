<?php
require("../ArtArchive.php");

$artwork = ArtworkDTO::CreateFrom($_POST);
$bdd = new DBService();

// $response = $bdd->AddArtwork($artwork);

header("Location: ./", false, 303);
die();
?>