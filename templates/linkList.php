<?php
/**
 * @param string $links	The list of link, with one link per line.
 */

$links = explode("\n", $links);
foreach($links as $key=>$value)
	$links[$key] = htmlspecialchars(trim($value));
?>

<div class="extlinkslist">
	<h3>External links :</h3>
	<ul>
		<?php
		foreach($links as $l) {
			?>
			<li>
				<a href="<?=$l?>" title="<?=$l?>"><?=$l?></a>
			</li>
			<?php
		}
		?>
	</ul>
</div>
