<?php
define("__ROOT__", __DIR__);
require_once("php/shorthands.php");
require_once("php/Url.php");
require_once("php/MediaType.php");
require_once("database/DBService.php");
require_once("php/PageBuilder.php");
require_once("auth/authenticator.php");

class ArtArchive {
	static $version = "0.9-indev.1";

	/** @var array */
	static $settings;
	/** @var DBService */
	static $database;

	/** @var bool */
	static $isWebmaster;

	/** @var Authenticator */
	static $authenticator;

	static public function RequireWebmaster() {
		if (!self::$isWebmaster) {
			self::$authenticator->ForceLogin();
			PageBuilder::ErrorDocument(401, "Unauthenticated");
			die;
		}
	}

	static public function GetSiteName(){
		return self::$settings["SiteName"];
	}
}

ArtArchive::$database = new DBService();
if (ArtArchive::$database->GetVersion() < DBService::$version){
	http_response_code(503);
	?>
	<p>
		The database has not been setup or needs an update.
		Please consult <a href="<?=URL::Wizard()?>">~The Wizard~</a>.
	</p>
	<?php
	die;
}

ArtArchive::$authenticator = new Authenticator("Webmaster");
ArtArchive::$isWebmaster = ArtArchive::$authenticator->CheckLogin();
if (isset($_GET["login"]))
	ArtArchive::RequireWebmaster();


ArtArchive::$settings = ArtArchive::$database->GetSettings(
	array(
		"SiteName" => "ArtArchive",
		"SiteLogo" => "/resources/logo.png",
		"stylesheet" => "/css/millenial.css",
		"ResultsPerPage" => 20,
		"AuthorName" => null,
		"AuthorEmail" => null,

		"tagMasonry" => 0,
		"tagLiquidity" => 16,
	)
);
?>
