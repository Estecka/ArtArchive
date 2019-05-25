<?php
define("__ROOT__", __DIR__);
require_once("shorthands.php");
require_once("Url.php");
require_once("database/DBService.php");
require_once("templates/PageBuilder.php");

class ArtArchive {
	/** @var array */
	static $settings;
	/** @var DBService */
	static $database;

	/** @var bool */
	static $isWebmaster;

	static public function RequireWebmaster() {
		if (!ArtArchive::$isWebmaster) {
			PageBuilder::ErrorDocument(403);
			die;
		}
	}

	static public function GetSiteName(){
		return self::$settings["SiteName"];
	}
}

ArtArchive::$database = new DBService();
ArtArchive::$settings = ArtArchive::$database->GetSettings(
	array(
		"SiteName" => "MyArtDump",
		"DummyLogin" => false,
	)
);

ArtArchive::$isWebmaster = ArtArchive::$settings["DummyLogin"];
?>