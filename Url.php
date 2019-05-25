<?php
class URL {
	static public function Home() : string {
		return "/site/";
	}
	static public function Login() : string {
		return "/site/login.php";
	}
	static public function Logout() : string {
		return "/site/login.php/?logout=true";
	}
	static public function SiteSettings() : string {
		return "/site/webmaster/settings.php";
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
	static public function Search(string $tags = null, $page = 0) : string {
		$url = "/site/search.php";
		if ($tags){
			$url .= "?tags=$tags";
			if ($page)
				$url .= "&page=$page";
		}
		return $url;
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
	static public function OrderCategory() : string {
		return "/site/categories.php";
	}
	static public function Category(string $slug) : string {
		return "/site/category/?category=$slug";
	}
	static public function EditCategory(string $slug) : string {
		return "/site/category/edit?category=$slug";
	}
	static public function SubmitCategory() : string {
		return "/site/submit/category/";
	}
}
?>
