<?php
/**
 * These variables should be defined before including the file:
 * @var string $title The Title of the setting page.
 * @var string $slug The id of the edited page in the config.
 */

require_once ".settings.php";

$page = new PageBuilder($title);
$page->StartPage();
	?>

	<form method="POST">

		<h2><label for="<?=$slug?>"><?=$title?></label></h2>
		<br/>
		<textarea name="pages[<?=$slug?>]" id="<?=$slug?>" placeholder="Write HTML here"><?=htmlspecialchars($pages[$slug])?></textarea>

		<input type="submit"/>
	</form>

	<?php
$page->EndPage();
?>
