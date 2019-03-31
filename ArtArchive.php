<?php
define("__ROOT__", __DIR__);
require_once("shorthands.php");
require_once("database/DBService.php");
require_once("templates/PageBuilder.php");

class URL {
	static public function Home() : string {
		return "/site/";
	}

	/** REGION ARTWORKS */
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

	/** REGION TAGS */
	static public function TagsHome() : string {
		return "/site/tags.php";
	}
	static public function Tag(string $slug) : string {
		return "/site/tag/?tag=$slug";
	}
	static public function EditTag(string $slug) : string {
		return "/site/tag/edit.php?tag=$slug";
	}
	static public function SubmitTag() : string {
		return "/site/submit/tag/";
	}

	/** REGION CATEGORIES */
	static public function Category(string $slug) : string {
		return "/site/category/?category=$slug";
	}
}
?>