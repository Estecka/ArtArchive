<?php
define("__ROOT__", __DIR__);
require_once("shorthands.php");
require_once("database/DBService.php");
require_once("templates/PageBuilder.php");

class URL {
	static public function Home() : string {
		return "/site/";
	}

	static public function Artwork(string $slug) : string {
		return "/site/art/?art=$slug";
	}
	static public function EditArt(string $slug) : string {
		return "/site/art/edit.php?art=$slug";
	}
	static public function SubmitArt() : string {
		return "/site/submit/art/";
	}

	static public function DeleteArt(string $slug) : string {
		return "/site/art/delete.php?art=$slug";
	}
}
?>