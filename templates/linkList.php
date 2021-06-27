<?php
/**
 * @param string $links	The list of link, with one link per line.
 */

$links = explode("\n", $links);
foreach($links as $key=>$value) {
	$matches = array();
	$r = preg_match("#^\s*\[(.*)\]\s*\((.*)\)\s*$#", $value, $matches); // "[label](link)"
	$l = array('label'=>NULL, 'adress'=>NULL);

	if ($r == false) {
		$l['adress'] = $value;
		$l['label']  = $l['adress'];
	} else {
		$l['adress'] = $matches[2];
		$l['label']  = $matches[1];
	}
	$links[$key] = $l;
}

function	GetFavicon(string $link) : ?string {
	$http = parse_url($link, PHP_URL_SCHEME);
	$host = parse_url($link, PHP_URL_HOST);
	if ($http && $host)
		return "$http://$host/favicon.ico";
	else
		return NULL;
}
?>

<div class="extlinkslist">
	<h3>External links :</h3>
	<ul>
		<?php
		foreach($links as $l) {
			$l['adress'] = htmlspecialchars(trim($l['adress']));
			$l['label']  = htmlspecialchars(trim($l['label' ]));
			$favicon = GetFavicon($l['adress']);
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
</div>
