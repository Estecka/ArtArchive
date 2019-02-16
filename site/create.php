<?php
require("../ArtArchive.php");

$page = new PageBuilder();
$page->title = "Submit";
$page->StartPage();
	$action = "insert.php";
	include("../templates/artworkForm.php");
$page->EndPage();
?>