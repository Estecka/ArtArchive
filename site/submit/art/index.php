<?php
require("../../../ArtArchive.php");

$page = new PageBuilder();
$page->title = "Submit";
$page->StartPage();
	$action = "/submit/art/insert.php";
	include(__ROOT__."/templates/artworkForm.php");
$page->EndPage();
?>