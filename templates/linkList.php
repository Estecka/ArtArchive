<?php
/**
 * @param string $links	The list of link, with one link per line.
 */

$links = explode("\n", $links);
foreach($links as $key=>$value)
	$links[$key] = htmlspecialchars(trim($value));

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
			$favicon = GetFavicon($l);
			if ($favicon)
				$favicon = "style=\"--link-favicon: url($favicon)\""
			?>
			<li <?=$favicon?>>
				<a href="<?=$l?>" title="<?=$l?>"><?=$l?></a>
			</li>
			<?php
		}
		?>
	</ul>
</div>
