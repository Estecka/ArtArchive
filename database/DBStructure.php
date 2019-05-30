<?php
require_once "config.php";

class DBStructure {
	/** @var PDO */
	static $pdo;
	static $error = null;

	/** @var int The version of the database php software. Not to be mistaken with the installed database structure version. */
	static public $version = 1;

	
	
	static $procedures = array(
		"Check_Slug",
	);
	static $tables = array(
		"artworks",
		"categories",
		"tags",
		
		"art-file",
		"art-tag",
		
		"pages",
		"settings",
	);

	static public function get_sql(string $name) {
		return file_get_contents(__DIR__."/Structure/".$name.".sql");
	}

	static public function StartTransaction(){
		self::$pdo->beginTransaction();
	}
	static public function Rollback(){
		self::$pdo->rollBack();
	}
	static public function Commit(){
		self::$pdo->commit();
	}

	static public function GetVersion(){
		try {
			return (float)self::$pdo->query("SELECT `value` FROM `settings` WHERE `name`='dbVersion';")->fetchColumn();
		} catch (PDOException $e) {
			return false;
		}
	}

	static public function DropAllEntities(){
		echo "<h3>Dropping...</h3>";
		self::$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
		foreach(self::$procedures as $item) {
			echo $item."<br/>";
			self::$pdo->exec("DROP PROCEDURE IF EXISTS `$item`");
		}
		foreach(self::$tables as $item){
			echo $item."<br/>";
			self::$pdo->exec("DROP TABLE IF EXISTS `$item`");
		}
	}

	static public function InstallAllentities(){
		echo "<h3>Installing procedures...</h3>";
		self::$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
		foreach(self::$procedures as $item){
			echo $item."<br/>";
			$sql = self::get_sql($item);
			self::$pdo->exec($sql);
		}
		echo "<h3>Installing tables...</h3>";
		foreach(self::$tables as $item){
			echo $item."<br/>";
			$sql = self::get_sql($item);
			self::$pdo->exec($sql);
		}
	}

	static public function Finalize(){
		$version = self::$version;
		self::$pdo->exec(
			"INSERT INTO `settings` (`name`, `value`) 
				VALUES ('dbVersion', '$version')
			ON DUPLICATE KEY UPDATE
				`value` = '$version';"
			);
	}
}

try {
	DBStructure::$pdo = new PDO(
		'mysql:host='.BDDHOST
			.';dbname='.BDDNAME
			.';charset=utf8',
		BDDID, 
		BDDPWD
	);
	DBStructure::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	DBStructure::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Required to work with sql generated by PhpMyAdmin
} catch (PDOException $e) {
	DBStructure::$error = $e;
}


?>