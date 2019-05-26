<?php
require_once "auth/authenticator.php";
require_once "database/DBService.php";

$auth = new Authenticator("Wizard");
if (!$auth->CheckLogin()){
	$auth->ForceLogin();
	die ("401");
}

try {
	$bdd = new DBService();
} catch (PDOException $e) {
	?>
	<p>
		Failed to connect to the database to database : 
		<br/><?=$e->getCode()?>
		<br/><?=$e->getMessage()?>
	</p>
	<?php
	die;
}

$installedVersion = $bdd->GetVersion();
?>