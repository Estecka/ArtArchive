<?php
require("../Artarchive.php");

$bdd = &ArtArchive::$database;


$page = new PageBuilder();
$page->title = "Login";
$page->StartPage();
	if (isset($_GET['logout']))
	{
		$bdd->SetSettings( array("DummyLogin" => false) );
		?>
		<h2>You have been logged out</h2>
		<a href="<?=URL::Home()?>">Why thank you my fair sir.</a>
		<?php
	}
	else if(!empty($_POST))
	{
		$bdd->SetSettings( array("DummyLogin" => true) );
		?>
		<h2>Thou hast been logged in</h2>
		<a href="<?=URL::Home()?>">Aw yesh</a>
		<?php
	}
	else if (ArtArchive::$isWebmaster)
	{
		?>
		<h2>You are currently logged the fuck in</h2>
		<form method="GET">
			<input type="hidden" name="logout" value="owo what's this? :3c" title="The ever creeping, all consuming hunger that lurks within us all."/>
			<input type="submit" value="gET Me oUT OF HERE !!?!"/>
		</form>
		<?php
	}
	else
	{
		?>
		<h2>I don't know you</h2>
		<form method="POST">
			<input type="submit" value="It's me! Your old pal "/>
			<input type="text" placeholder="insert name here" name="bneh"/>
			<input type="submit" value=" !"/>
		</form>
		<?php
	}

$page->EndPage();
?>
