<?php
/**
 * @param PageBuilder $this
 * @param string $links	The list of link, with one link per line.
 */

require_once __ROOT__."/php/SocialIcon.php";

$lines = explode("\n", $links);
$links = array();
foreach($lines as $key=>$value) {
	if (empty(trim($value)))
		continue;

	$matches = array();
	$r = preg_match("#^\s*\[(.*)\]\s*\((.*)\)\s*$#", $value, $matches); // "[label](link)"
	$l = array('label'=>NULL, 'adress'=>NULL);

	if ($r == false) {
		$l['adress'] = $value;
		if (preg_match("#^https?:\/\/(.+)$#", $l['adress'], $matches))
			$l['label'] = $matches[1];
		else
			$l['label'] = $l['adress'];
	} else {
		$l['adress'] = $matches[2];
		$l['label']  = $matches[1];
	}
	$links[] = $l;
}
?>

<ul class="extlinkslist">
	<?php
	foreach($links as $l) {
		$l['adress'] = htmlspecialchars(trim($l['adress']));
		$l['label']  = htmlspecialchars(trim($l['label' ]));
		$favicon = SocialIcon::GetFavicon($l['adress']);
		if ($favicon)
			$favicon = "style=\"--link-favicon: url($favicon)\""
		?>
		<li <?=$favicon?>>
			<a href="<?=$l['adress']?>" title="<?=$l['label']?>">
				<?=$l['label']?>
			</a>
		</li>
		<?php
	}
	?>
</ul>
