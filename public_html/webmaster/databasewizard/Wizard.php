<?php
define("__ROOT__", __DIR__."/../../..");
require_once __ROOT__."/auth/authenticator.php";
require_once __ROOT__."/database/DBStructure.php";

$auth = new Authenticator("Wizard");
if (!$auth->CheckLogin()){
	$auth->ForceLogin();
	die ("401");
}


if (DBStructure::$error != null){
	?>
	<p>
		Failed to connect to the database to database : 
		<br/><?=DBStructure::$error->getCode()?>
		<br/><?=DBStructure::$error->getMessage()?>
	</p>
	<?php
	die;
}

$installedVersion = DBStructure::GetVersion();
?>