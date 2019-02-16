<?php
require("../ArtArchive.php");

$page = new PageBuilder();
$page->title = "Submit";
$page->StartPage();
	include("../templates/artworkForm.php");
$page->EndPage();
?>