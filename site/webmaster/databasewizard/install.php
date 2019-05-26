<?php
require_once "../../../Wizard.php";

$overwrite = isset($_GET['overwrite']);

if (!$overwrite && $installedVersion != 0){
	header("Location:index.php", false, 303);
	exit;
}

function get_sql(string $name) {
	return file_get_contents("../../../database/Structure/".$name.".sql");
}

$procedures = array(
	"Check_Slug",
);
$tables = array(
	"artworks",
	"categories",
	"tags",

	"art-file",
	"art-tag",

	"pages",
	"settings",
);


$bdd->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Required to work with sql generated by PhpMyAdmin
$bdd->StartTransaction();
try {
	echo "<h3>Installing procedures...</h3>";
	foreach($procedures as $item){
		echo $item."<br/>";
		if ($overwrite)
			$bdd->pdo->exec("DROP PROCEDURE IF EXISTS `$item`;");
		$sql = get_sql($item);
		// var_dump($sql);
		$bdd->pdo->exec($sql);
	}
	echo "<h3>Installing tables...</h3>";
	foreach($tables as $item){
		echo $item."<br/>";
		if ($overwrite)
			$bdd->pdo->exec("DROP TABLE IF EXISTS `$item`;");
		$sql = get_sql($item);
		// var_dump($sql);
		$bdd->pdo->exec($sql);
	}

	$bdd->SetSettings(array("dbVersion" => DBService::$version));
} catch (PDOException $e) {
	echo "A database exception occured : <br/>";
	echo $e->getCode()."<br/>";
	echo $e->getMessage()."<br/>";

	if (!$overwrite) {
		?>
		<br/>If the error says the table or procedure already exists in the database, you may try again by overwritting
		<br/><strong>This is a destructive operation; any data contained in these tables will be iremediably lost. Please check twice before proceeding.</strong>
		<form method=GET>
			<input type=checkbox id=overwrite name=overwrite>
			<label for=overwrite>I understand</label>
			<br/>
			<input type=submit value="Drop and retry"/>
		</form>
		<?php
	}

	$bdd->Rollback();
	die;
} catch (Exception $e) {
	echo "An unexpected exception occured : <br/>";
	$bdd->Rollback();
	echo $e;
	die;
}
$bdd->CommitTransaction();

echo "<br/><strong>Success !</strong>";
echo "<br/><span title='note: keikaku means plan'>All according to keikaku</span>";
echo "<br/>You are free to remove the the `databasewizard` folder from the website's structure, you won't need it anymore."
?>