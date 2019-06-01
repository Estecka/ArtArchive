<?php
class URL {
	static public function Home() : string {
		return "/";
	}
	static public function About() : string {
		return "/about.php";
	}
	static public function Login() : string {
		return URL::Home()."?login";
	}
	static public function SiteSettings() : string {
		return "/webmaster/settings.php";
	}

	static public function Wizard() : string {
		return "/webmaster/databasewizard/index.php";
	}

	static public function SourceCode() : string {
		return "/https://github.com/Estecka/ArtArchive";
	}

	/** REGION ARTWORKS */
	static public function Artwork(string $slug) : string {
		return "/art/?art=$slug";
	}
	static public function EditArt(string $slug) : string {
		return "/art/edit.php?art=$slug";
	}
	static public function SubmitArt() : string {
		return "/submit/art/";
	}

	static public function DeleteArt(string $slug) : string {
		return "/art/delete.php?art=$slug";
	}
	static public function Search(string $tags = null, $page = 0) : string {
		$url = "search.php";
		if ($tags){
			$url .= "?tags=$tags";
			if ($page)
				$url .= "&page=$page";
		}
		return $url;
	}

	/** REGION TAGS */
	static public function TagsHome() : string {
		return "/tags.php";
	}
	static public function Tag(string $slug) : string {
		return "/tag/?tag=$slug";
	}
	static public function EditTag(string $slug) : string {
		return "/tag/edit.php?tag=$slug";
	}
	static public function SubmitTag() : string {
		return "/submit/tag/";
	}

	/** REGION CATEGORIES */
	static public function OrderCategory() : string {
		return "/categories.php";
	}
	static public function Category(string $slug) : string {
		return "/category/?category=$slug";
	}
	static public function EditCategory(string $slug) : string {
		return "/category/edit?category=$slug";
	}
	static public function SubmitCategory() : string {
		return "/submit/category/";
	}
}
?>
