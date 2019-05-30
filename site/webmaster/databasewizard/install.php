<?php
require_once "Wizard.php";

$overwrite = isset($_GET['overwrite']);

if (!$overwrite && $installedVersion != 0){
	header("Location:index.php", false, 303);
	exit;
}


DBStructure::StartTransaction();
try {
	if ($overwrite)
		DBStructure::DropAllEntities();

	DBStructure::InstallAllentities();
	DBStructure::Finalize();

} catch (PDOException $e) {
	echo "A database exception occured : <br/>";
	echo $e->getCode()."<br/>";
	echo $e->getMessage()."<br/>";

	if (!$overwrite) {
		?>
		<br/>If the error says the table or procedure already exists in the database, you may try overwriting it.
		<br/><strong>This is a destructive operation; if you are already storing anything in this database, it may become iremediably lost. Please check twice before proceeding.</strong>
		<form method=GET>
			<input type=submit value="Retry"/>
			<br/>
			<input type=checkbox id=overwrite name=overwrite>
			<label for=overwrite>And drop the following tables/procedures : </label>
			<?php
			foreach(array_merge(DBStructure::$procedures, DBStructure::$tables) as $item)
				echo "<br/>".$item;
			?>
		</form>
		<?php
	}

	DBStructure::Rollback();
	die;
} catch (Exception $e) {
	echo "An unexpected exception occured : <br/>";
	DBStructure::Rollback();
	echo $e;
	die;
}
DBStructure::Commit();

echo "<br/><strong>Success !</strong>";
echo "<br/><span title='tanslator's note: keikaku means plan'>All according to keikaku</span>";
echo "<br/>You are free to remove the `databasewizard` folder from the website's structure, you won't need it anymore."
?>