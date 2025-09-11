<?php

// require_once "ArtArchive.php";

class SocialIcon
{
	static private $settingPrefix = "socialIcon";
	static private $defaultIcon = "/resources/link.png";
	static private $icons = array();

	static public function DefaultValues() : array {
		return array(
			"artfight.net"   => "https://artfight.net/favicon.svg",
			"artstation.com" => "https://artstation.com/favicon.ico",
			"bsky.app"       => "https://web-cdn.bsky.app/static/favicon-16x16.png",
			"deviantart.com" => "https://deviantart.com/favicon.ico",
			"fav.me"         => "https://deviantart.com/favicon.ico",
			"sta.sh"         => "https://deviantart.com/favicon.ico",
			"estecka.fr"     => "https://estecka.fr/favicon.ico",
			"github.com"     => "https://github.com/favicon.ico",
			"itch.io"        => "https://itch.io/favicon.ico",
			"modrinth.com"   => "https://modrinth.com/favicon.ico",
			"tumblr.com"     => "https://tumblr.com/favicon.ico",
			"twitter.com"    => "https://twitter.com/favicon.ico",
			"x.com"          => "https://twitter.com/favicon.ico",
			"soundcloud.com" => "https://soundcloud.com/favicon.ico",
			"youtu.be"       => "https://youtube.com/favicon.ico",
			"youtube.com"    => "https://youtube.com/favicon.ico",
		);
	}

	static public function GetFavicon(string $url) : string {
		$urlDomain = preg_replace("#^https?:\/\/([^\/?]+).*#", "$1", $url, 1);
		foreach (self::$icons as $iconDomain => $favicon)
			if ($urlDomain == $iconDomain || endsWith($urlDomain, ".$iconDomain"))
				return $favicon;

		return self::$defaultIcon;
	}

	static public function InitFromSettings(){
		self::$icons = array();
		foreach (ArtArchive::$settings as $key=>$favicon)
		if (startsWith($key, self::$settingPrefix))
		{
			$domain = substr($key, self::$settingPrefix);
			self::$icons[$domain] = $favicon;
		}
		if (empty(self::$icons))
			self::$icons = self::DefaultValues();
	}

	static private function ToForm($icons) : string
	{
		$lengthMax = 0;
		foreach ($icons as $domain => $icon)
			$lengthMax = max($lengthMax, strlen($domain));

		$result = "";
		foreach ($icons as $domain => $icon){
			$result .= "$domain";
			for ($i=strlen($domain); $i<$lengthMax; ++$i)
				$result .= " ";
			$result .= "  $icon\n";
		}
		return $result;
	}

	static public function GetFormDefault() : string
	{
		return self::ToForm(self::$icons);
	}

	static public function GetFormPlaceholder() : string
	{
		return self::ToForm(self::DefaultValues());
	}

	static public function FormToSettings(string $string, array &$settings) : void {
		$lines = explode("\n", $string);
		foreach ($lines as $entry) {
			$entry = trim($entry);
			$values = explode(" ", $entry, 2);

			$domain = $values[0];
			$icon = $values[1];

			$settings[self::$settingPrefix.$domain] = $icon;
		}
	}

	static public function InitFromForm(string $string) : void {
		self::$icons = array();
		$lines = explode("\n", $string);
		foreach ($lines as $entry)
		{
			$entry = trim($entry);
			if (empty($entry))
				continue;
			$values = explode(" ", $entry, 2);
			if (!isset($values[1]))
				continue;

			$domain = trim($values[0]);
			$icon = trim($values[1]);
			if (empty($domain) || empty($icon))
				continue;

			self::$icons[$domain] = $icon;
		}
		if (empty(self::$icons))
			self::$icons = self::DefaultValues();
	}
}

SocialIcon::InitFromForm(ArtArchive::$database->GetPage("socialFavicons"));

?>
