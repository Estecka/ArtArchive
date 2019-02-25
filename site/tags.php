<?php
require("../Artarchive.php");
$bdd = new DBService();

$tags = $bdd->GetAllTags();

$page = new PageBuilder();
$page->StartPage();
?>
<a href="<?=URL::SubmitTag()?>">Create</a>
<hr/>
<?php

	/** @var $tag TagDTO */
	foreach($tags as $tag){
		$slug = $tag->slug;
		?>
		<br/>
		<a href="<?=URL::Tag($slug)?>"><?=$slug?></a>
		<?php
	}

$page->EndPage();
?>
