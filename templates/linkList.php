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
	$matches = array();
	if (preg_match("#https?:\/\/(?:[\w-]*\.)?(\w+\.\w+)(?:\/.*)?#", $link, $matches))
	switch ($matches[1]) {
		case "fav.me":
		case "sta.sh":
		case "deviantart.com":
			return "https://deviantart.com/favicon.ico";
		case "artstation.com":
			return "https://dartstation.com/favicon.ico";
		case "tumblr.com":
			return "https://www.tumblr.com/favicon.ico";
		case "youtu.be":
		case "youtube.com":
			return "https://www.youtube.com/favicon.ico";
		case "twitter.com":
			return "https://twitter.com/favicon.ico";
		case "soundcloud.com":
			return "https://soundcloud.com/favicon.ico";
	}
	return "/resources/link.png";
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
