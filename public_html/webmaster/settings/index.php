<?php
require_once ".settings.php";

$page = new PageBuilder("Control Panel");
$page->StartPage();
	?>
	<article>
		<h1>Control Panel</h1>

		<h3>Site Identity</h3>
		<ul>
			<li><a href="site-info.php">Site-wide info</a></li>
			<li><a href="site-info.php#RssSettings">RSS info</a></li>
			<li><a href="homepage.php">Home page message</a></li>
			<li><a href="aboutpage.php">About page</a></li>
		</ul>

		<h3><a href="theme.php">Theme & Layout</a></h3>

		<h3><a href="legacy.php">Legacy Panel</a></h3>
	</article>

	<?php
$page->EndPage();
?>
