<?php
require_once "Wizard.php";

function	Fail($err, $message) {
	http_response_code($err);
	echo $message;
	die;
}

if (($installedVersion!=(int)$installedVersion) || DBStructure::$version<=$installedVersion) 
	Fail(500, "The installed database has an unexpected version number : \"$installedVersion\". The upgrade was cancelled.");

for ($i=$installedVersion+1; $i<=DBStructure::$version; $i++) {
	echo "Upgrading to $i...<br/>";
	$sql = file_get_contents(__ROOT__."/database/upgrades/$i.sql");
	if ($sql === false)
		Fail(500, "Missing upgrade files for dbVersion $i.<br/> The upgrade was halted at the preceding version, and may resume after the file is restored.");
	try {
		DBStructure::$pdo->exec($sql);
	} catch (PDOException $e) {
		Fail(500, "Unexpected pdo error: $e");
	}
}

echo "Success !";

?>