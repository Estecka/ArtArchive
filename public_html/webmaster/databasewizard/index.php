<?php
require_once "Wizard.php";

// Existing version found
if ($installedVersion != 0)
{
	print "<p>";
	if ($installedVersion == DBStructure::$version) {
		?>
		The database seems to have already been set up.
		<br/>
		Do you want to perform a clean install anyway ?
		<?php
	}
	else if ($installedVersion > DBStructure::$version){
		?>
		A more recent version of the database seems to be already set up.
		<br/>Downgrade is not supported. Do you want to perform a clean install ?
		<?php
	}
	else if  ($installedVersion < DBStructure::$version){
		?>
		An older version of the database seem to be already set up.
		<br/>
		<br/>Automatic update is possible. It is recommended to backup your database before updating.
		<br/><a href="upgrade.php"><input type=submit value="Update now"/></a>
		<hr/>
		<br/>Else do you wish to perform a clean install?
		<?php
	}
	?>
	<br/><strong>Clean install is a destructive operation; if you are already storing anything in this database, it may become iremediably lost. Please check twice before proceeding.</strong>
	<form method=GET action="install.php">
		<input type=submit value="Install"/>
		<br/>
		<input type=checkbox name=overwrite id=overwrite/>
		<label for=overwrite>And drop the following tables/procedures : </label>
		<?php
		foreach(array_merge(DBStructure::$procedures, DBStructure::$tables) as $item)
			echo "<br/>".$item;
		?>
	</form>
	<?php
	print "</p>";
}
// No existing version found
else
{
	?>
	This Wizard will setup the database for use with ArtArchive.
	<br/>Proceed ?
	<form action="install.php">
		<input type=submit value="Yes please,"/>
	<form>
	<?php
}

?>