<?php
class URL {
	static public function Protocol() : string {
		if ($_SERVER['SERVER_PORT'] == 443 && value($_SERVER['HTTPS']) !== "off")
			return "https://";
		else
			return "http://";
	}
	static public function Root() : string {
		return URL::Protocol().$_SERVER['SERVER_NAME'];
	}

	static public function Absolute(string $url) {
		if (substr($url, 0, 7) == "http://" || substr($url, 0, 8) == "https://")
			return $url;
		else
			return URL::Root().$url;
	}

	static public function Home() : string {
		return "/";
	}
	static public function About() : string {
		return "/about.php";
	}
	static public function Login() : string {
		return "?login";
	}
	static public function SiteSettings() : string {
		return "/webmaster/settings.php";
	}

	static public function Wizard() : string {
		return "/webmaster/databasewizard/index.php";
	}

	static public function SourceCode() : string {
		return "https://github.com/Estecka/ArtArchive";
	}

	/** REGION ARTWORKS */
	static public function Artwork(string $slug) : string {
		return "/art/$slug/";
	}
	static public function EditArt(string $slug) : string {
		return "/art/$slug/edit.php";
	}
	static public function SubmitArt() : string {
		return "/submit/art/";
	}

	static public function DeleteArt(string $slug) : string {
		return "/art/$slug/delete.php";
	}
	static public function Search(string $tags = null, $page = 0) : string {
		$url = "/search.php";
		if ($tags){
			$url .= "?tags=$tags";
			if ($page)
				$url .= "&page=$page";
		}
		return $url;
	}

	static public function Media(string $path) : string {
		if (preg_match("#^https?://.+#", $path))
			return $path;
		else
			return "/storage/$path";
	}
	static public function Thumb(string $path) : string {
		if (preg_match("#^https?://.+#", $path))
			return $path;
		else
			return "/thumbs/$path";
	}

	/** REGION TAGS */
	static public function TagsHome() : string {
		return "/tags.php";
	}
	static public function Tag(string $slug) : string {
		return "/tag/$slug/";
	}
	static public function EditTag(string $slug) : string {
		return "/tag/$slug/edit.php";
	}
	static public function DeleteTag(string $slug) : string {
		return "/tag/$slug/delete.php";
	}
	static public function SubmitTag() : string {
		return "/submit/tag/";
	}

	/** REGION CATEGORIES */
	static public function OrderCategory() : string {
		return "/categories.php";
	}
	static public function Category(string $slug) : string {
		return "/category/$slug/";
	}
	static public function EditCategory(string $slug) : string {
		return "/category/$slug/edit.php";
	}
	static public function DeleteCategory(string $slug) : string {
		return "/category/$slug/delete.php";
	}
	static public function SubmitCategory() : string {
		return "/submit/category/";
	}
}
?>
